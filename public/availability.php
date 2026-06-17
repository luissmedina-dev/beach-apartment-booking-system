<?php
    require_once("../config/connection.php");
    require_once("../templates/header.php");
    require_once("../templates/navbar.php");

    // ── Buscar datas ocupadas no banco ──────────────────────────────
    // Apenas reservas ativas (não canceladas)
    $stmt = $conn->prepare("
        SELECT checkin_date, checkout_date
        FROM reservations
        WHERE status != 'Cancelado'
    ");
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Expande cada reserva em dias individuais ocupados
    $datasOcupadas = [];
    foreach ($reservas as $r) {
        $inicio  = new DateTime($r['checkin_date']);
        $fim     = new DateTime($r['checkout_date']);
        $fim->modify('-1 day'); // checkout não bloqueia o dia de saída
        $periodo = new DatePeriod(
            $inicio,
            new DateInterval('P1D'),
            $fim->modify('+1 day')
        );
        foreach ($periodo as $dia) {
            $datasOcupadas[] = $dia->format('Y-m-d');
        }
    }
    $datasOcupadas     = array_values(array_unique($datasOcupadas));
    $datasOcupadasJson = json_encode($datasOcupadas);
    // ────────────────────────────────────────────────────────────────
?>

<section class="avail-page">

    <div class="avail-header">
        <span class="section-tag">Disponibilidade</span>
        <h1>Quando você quer<br>chegar em Bombinhas?</h1>
        <p>Consulte os dias disponíveis e solicite sua reserva. Confirmação feita diretamente com os proprietários.</p>
    </div>

    <div class="avail-layout">

        <!-- ── Calendário ── -->
        <div class="avail-calendar-wrap">

            <!-- Navegação de mês -->
            <div class="avail-cal-header">
                <button type="button" class="avail-nav-btn" id="avPrev" aria-label="Mês anterior">&#8592;</button>
                <span class="avail-cal-month" id="avTitle"></span>
                <button type="button" class="avail-nav-btn" id="avNext" aria-label="Próximo mês">&#8594;</button>
            </div>

            <!-- Grade gerada por JS -->
            <div class="avail-cal-grid" id="avGrid"></div>

            <!-- Legenda -->
            <div class="avail-legend">
                <span class="legend-item">
                    <span class="legend-dot legend-dot--free"></span>Disponível
                </span>
                <span class="legend-item">
                    <span class="legend-dot legend-dot--booked"></span>Reservado
                </span>
                <span class="legend-item">
                    <span class="legend-dot legend-dot--past"></span>Passado
                </span>
            </div>
        </div>

        <!-- ── Painel lateral ── -->
        <div class="avail-info">
            <div class="avail-info-card">
                <h3>Como funciona</h3>
                <ol class="avail-steps">
                    <li>
                        <span class="step-num">1</span>
                        <div>
                            <strong>Verifique a disponibilidade</strong>
                            <p>Consulte o calendário ao lado e escolha o período desejado.</p>
                        </div>
                    </li>
                    <li>
                        <span class="step-num">2</span>
                        <div>
                            <strong>Solicite a reserva</strong>
                            <p>Faça login e envie sua solicitação com as datas escolhidas.</p>
                        </div>
                    </li>
                    <li>
                        <span class="step-num">3</span>
                        <div>
                            <strong>Aguarde a confirmação</strong>
                            <p>Os proprietários confirmam a reserva e entram em contato.</p>
                        </div>
                    </li>
                </ol>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="../public/request_reservation.php" class="avail-cta-btn">Solicitar reserva</a>
                <?php else: ?>
                    <a href="../client/login.php" class="avail-cta-btn">Solicitar reserva</a>
                    <p class="avail-register-hint">Não tem conta? <a href="../client/register.php">Cadastre-se grátis</a></p>
                <?php endif; ?>
            </div>

            <div class="avail-contact-card">
                <p>Prefere falar diretamente?</p>
                <a href="https://wa.me/5547999999999?text=Olá,%20gostaria%20de%20consultar%20a%20disponibilidade%20do%20apartamento."
                   target="_blank" class="avail-whatsapp-btn">
                    💬 Chamar no WhatsApp
                </a>
            </div>
        </div>

    </div>
</section>

<script>
(function () {
    // ── Dados do PHP ──────────────────────────────────────────────
    const BOOKED = new Set(<?= $datasOcupadasJson ?>);
    const TODAY  = '<?= date('Y-m-d') ?>';

    const MESES = [
        'Janeiro','Fevereiro','Março','Abril','Maio','Junho',
        'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'
    ];
    const SEMANA = ['DOM','SEG','TER','QUA','QUI','SEX','SÁB'];

    // ── Estado ────────────────────────────────────────────────────
    const todayDate = new Date(TODAY + 'T00:00:00');
    let viewYear    = todayDate.getFullYear();
    let viewMonth   = todayDate.getMonth(); // 0-indexed

    // ── Elementos ─────────────────────────────────────────────────
    const title  = document.getElementById('avTitle');
    const grid   = document.getElementById('avGrid');
    const btnPrev = document.getElementById('avPrev');
    const btnNext = document.getElementById('avNext');

    // ── Utilitário: YYYY-MM-DD local ──────────────────────────────
    function toYMD(y, m, d) {
        return `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    }

    // ── Renderizar ────────────────────────────────────────────────
    function render() {
        title.textContent = `${MESES[viewMonth]} ${viewYear}`;
        grid.innerHTML    = '';

        // Cabeçalho da semana
        SEMANA.forEach(d => {
            const el = document.createElement('div');
            el.className   = 'avail-weekday';
            el.textContent = d;
            grid.appendChild(el);
        });

        const firstDay    = new Date(viewYear, viewMonth, 1).getDay();
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        // Células vazias para alinhar ao dia da semana correto
        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'avail-day avail-day--empty';
            grid.appendChild(el);
        }

        // Dias do mês
        for (let day = 1; day <= daysInMonth; day++) {
            const ymd    = toYMD(viewYear, viewMonth, day);
            const isPast = ymd < TODAY;
            const isBook = BOOKED.has(ymd);

            const el = document.createElement('div');
            el.className = 'avail-day';

            const num = document.createElement('span');
            num.className   = 'avail-day-num';
            num.textContent = day;
            el.appendChild(num);

            if (isPast) {
                el.classList.add('avail-day--past');
            } else if (isBook) {
                el.classList.add('avail-day--booked');
            } else {
                el.classList.add('avail-day--free');
                const dot = document.createElement('span');
                dot.className = 'avail-day-dot';
                el.appendChild(dot);
            }

            grid.appendChild(el);
        }
    }

    // ── Navegação ─────────────────────────────────────────────────
    btnPrev.addEventListener('click', () => {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        render();
    });

    btnNext.addEventListener('click', () => {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        render();
    });

    // ── Init ──────────────────────────────────────────────────────
    render();
})();
</script>

<?php require_once("../templates/footer.php"); ?>
