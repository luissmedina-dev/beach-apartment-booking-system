<?php

require_once("../config/connection.php");

session_start();

$errors  = [];
$success = false;

if(!isset($_SESSION['user_id'])) {
    header("Location: ../client/login.php");
    exit();
}

// ── Buscar datas ocupadas no banco ──────────────────────────────────────────
// Considera reservas que NÃO foram canceladas
$stmtOcupadas = $conn->prepare("
    SELECT checkin_date, checkout_date
    FROM reservations
    WHERE status != 'Cancelado'
");
$stmtOcupadas->execute();
$reservasOcupadas = $stmtOcupadas->fetchAll(PDO::FETCH_ASSOC);

// Gera array de datas ocupadas (YYYY-MM-DD) para passar ao JavaScript
$datasOcupadas = [];
foreach ($reservasOcupadas as $r) {
    $inicio = new DateTime($r['checkin_date']);
    $fim    = new DateTime($r['checkout_date']);
    $fim->modify('-1 day'); // checkout não bloqueia o dia de saída
    $periodo = new DatePeriod($inicio, new DateInterval('P1D'), $fim->modify('+1 day'));
    foreach ($periodo as $dia) {
        $datasOcupadas[] = $dia->format('Y-m-d');
    }
}
$datasOcupadasJson = json_encode(array_values(array_unique($datasOcupadas)));
// ────────────────────────────────────────────────────────────────────────────

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $checkin  = filter_input(INPUT_POST, 'checkin');
    $checkout = filter_input(INPUT_POST, 'checkout');

    if(empty($checkin) || empty($checkout)) {
        $errors[] = 'Preencha as duas datas.';
    } elseif($checkin >= $checkout) {
        $errors[] = 'A data de saída deve ser posterior à data de entrada.';
    } elseif($checkin < date('Y-m-d')) {
        $errors[] = 'A data de entrada não pode ser no passado.';
    }

    if(empty($errors)) {
        $user_id     = $_SESSION['user_id'];
        $status      = 'Solicitado';
        $daily_price = 300;

        $entrada     = new DateTime($checkin);
        $saida       = new DateTime($checkout);
        $days        = $entrada->diff($saida)->days;
        $total_price = $days * $daily_price;

        $stmt = $conn->prepare('INSERT INTO reservations
                                (user_id, checkin_date, checkout_date, total_price, status)
                                VALUES
                                (:user_id, :checkin, :checkout, :total_price, :status)');
        $stmt->bindParam(':user_id',     $user_id);
        $stmt->bindParam(':checkin',     $checkin);
        $stmt->bindParam(':checkout',    $checkout);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->bindParam(':status',      $status);
        $stmt->execute();

        $success = true;
    }
}

require_once('../templates/header.php');
require_once('../templates/navbar.php');
?>

<section class="reservation-page">
    <div class="reservation-layout">

        <!-- ── Coluna principal: calendário + formulário ── -->
        <div class="reservation-form-wrap">
            <span class="section-tag">Solicitar reserva</span>
            <h1>Escolha as suas datas</h1>
            <p>Clique na data de entrada e depois na data de saída. Dias em vermelho já estão reservados.</p>

            <?php if(!empty($errors)): ?>
                <div class="res-errors">
                    <?php foreach($errors as $error): ?>
                        <p>⚠ <?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="res-success">
                    <div class="res-success-icon">✓</div>
                    <h3>Solicitação enviada!</h3>
                    <p>Recebemos seu pedido e entraremos em contato em breve para confirmar a reserva.</p>
                    <a href="../client/my_reservations.php" class="res-success-link">Ver minhas reservas →</a>
                </div>
            <?php else: ?>

                <form action="" method="POST" id="resForm">

                    <!-- Inputs reais enviados ao PHP (hidden) -->
                    <input type="hidden" name="checkin"  id="checkin"  value="<?= htmlspecialchars($_POST['checkin']  ?? '') ?>">
                    <input type="hidden" name="checkout" id="checkout" value="<?= htmlspecialchars($_POST['checkout'] ?? '') ?>">

                    <!-- ── CALENDÁRIO ── -->
                    <div class="cal-wrap">

                        <!-- Cabeçalho de navegação -->
                        <div class="cal-nav">
                            <button type="button" class="cal-nav-btn" id="calPrev" aria-label="Mês anterior">&#8592;</button>
                            <span class="cal-title" id="calTitle"></span>
                            <button type="button" class="cal-nav-btn" id="calNext" aria-label="Próximo mês">&#8594;</button>
                        </div>

                        <!-- Grade de dias -->
                        <div class="cal-grid" id="calGrid">
                            <!-- preenchido por JS -->
                        </div>

                        <!-- Legenda -->
                        <div class="cal-legend">
                            <span class="cal-legend-item">
                                <span class="cal-legend-dot cal-legend-dot--free"></span>Disponível
                            </span>
                            <span class="cal-legend-item">
                                <span class="cal-legend-dot cal-legend-dot--booked"></span>Reservado
                            </span>
                            <span class="cal-legend-item">
                                <span class="cal-legend-dot cal-legend-dot--past"></span>Passado
                            </span>
                            <span class="cal-legend-item">
                                <span class="cal-legend-dot cal-legend-dot--selected"></span>Selecionado
                            </span>
                        </div>

                        <!-- Campos visuais de data + botão limpar -->
                        <div class="cal-date-bar">
                            <div class="cal-date-field" id="calCheckinDisplay">
                                <span class="cal-date-label">DATA DE ENTRADA</span>
                                <span class="cal-date-value" id="displayCheckin">—</span>
                            </div>
                            <span class="cal-date-arrow">→</span>
                            <div class="cal-date-field" id="calCheckoutDisplay">
                                <span class="cal-date-label">DATA DE SAÍDA</span>
                                <span class="cal-date-value" id="displayCheckout">—</span>
                            </div>
                            <button type="button" class="cal-clear-btn" id="calClear">Limpar</button>
                        </div>
                    </div>
                    <!-- fim .cal-wrap -->

                    <!-- Preview de preço -->
                    <div class="res-preview" id="resPreview" style="display:none">
                        <div class="res-preview-row">
                            <span>Diária</span>
                            <span>R$ 300,00</span>
                        </div>
                        <div class="res-preview-row">
                            <span id="previewNights">— noites</span>
                            <span id="previewTotal">R$ —</span>
                        </div>
                        <div class="res-preview-total">
                            <span>Total estimado</span>
                            <span id="previewTotalBold">R$ —</span>
                        </div>
                    </div>

                    <button type="submit" class="res-submit-btn" id="resSubmit" disabled>Solicitar reserva</button>
                    <p class="res-disclaimer">Sua solicitação ficará com status "Solicitado" até ser confirmada pelos proprietários.</p>
                </form>

            <?php endif; ?>
        </div>
        <!-- fim .reservation-form-wrap -->

        <!-- ── Painel lateral ── -->
        <div class="reservation-info">
            <div class="res-info-card">
                <h3>O apartamento</h3>
                <ul class="res-info-list">
                    <li>🛏 2 quartos — até 6 hóspedes</li>
                    <li>🚗 Garagem privativa inclusa</li>
                    <li>📶 Wi-Fi de alta velocidade</li>
                    <li>🏖 A 250m da praia</li>
                    <li>🍳 Cozinha totalmente equipada</li>
                </ul>
                <a href="../public/gallery.php" class="res-gallery-link">Ver fotos do apartamento →</a>
            </div>
            <div class="res-rules-card">
                <p>Leia as regras antes de reservar</p>
                <a href="../public/rules.php" class="res-rules-link">Ver regras de utilização →</a>
            </div>
        </div>

    </div>
</section>

<!-- ══════════════════════════════════════════
     JAVASCRIPT DO CALENDÁRIO
     Mantém toda lógica PHP; apenas controla UI
     ══════════════════════════════════════════ -->
<script>
(function () {
    // ── Configuração ──────────────────────────────────────────────────────
    const DAILY_PRICE    = 300;
    const TODAY_STR      = '<?= date('Y-m-d') ?>';
    const BOOKED_DATES   = new Set(<?= $datasOcupadasJson ?>);

    const MESES = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho',
                   'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
    const DIAS_SEMANA = ['DOM','SEG','TER','QUA','QUI','SEX','SÁB'];

    // ── Estado ────────────────────────────────────────────────────────────
    const today   = new Date(TODAY_STR + 'T00:00:00');
    let viewYear  = today.getFullYear();
    let viewMonth = today.getMonth(); // 0-indexed

    let checkinDate  = null; // Date | null
    let checkoutDate = null; // Date | null

    // ── Elementos ─────────────────────────────────────────────────────────
    const calTitle    = document.getElementById('calTitle');
    const calGrid     = document.getElementById('calGrid');
    const btnPrev     = document.getElementById('calPrev');
    const btnNext     = document.getElementById('calNext');
    const btnClear    = document.getElementById('calClear');
    const inputCheckin  = document.getElementById('checkin');
    const inputCheckout = document.getElementById('checkout');
    const displayCheckin  = document.getElementById('displayCheckin');
    const displayCheckout = document.getElementById('displayCheckout');
    const resPreview  = document.getElementById('resPreview');
    const resSubmit   = document.getElementById('resSubmit');

    // ── Utilitários ───────────────────────────────────────────────────────
    function toYMD(d) {
        // Formata Date → 'YYYY-MM-DD' no fuso local
        const yy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        return `${yy}-${mm}-${dd}`;
    }

    function toBR(d) {
        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        return `${dd}/${mm}/${d.getFullYear()}`;
    }

    function isBefore(a, b) { return toYMD(a) < toYMD(b); }
    function isSame(a, b)   { return toYMD(a) === toYMD(b); }

    // Verifica se alguma data no intervalo [start, end) está bloqueada
    function rangeHasBooked(start, end) {
        const cur = new Date(start);
        while (isBefore(cur, end)) {
            if (BOOKED_DATES.has(toYMD(cur))) return true;
            cur.setDate(cur.getDate() + 1);
        }
        return false;
    }

    // ── Renderizar calendário ─────────────────────────────────────────────
    function renderCalendar() {
        calTitle.textContent = `${MESES[viewMonth]} ${viewYear}`;
        calGrid.innerHTML = '';

        // Cabeçalho da semana
        DIAS_SEMANA.forEach(d => {
            const el = document.createElement('div');
            el.className = 'cal-weekday';
            el.textContent = d;
            calGrid.appendChild(el);
        });

        const firstDay    = new Date(viewYear, viewMonth, 1).getDay(); // 0=Dom
        const daysInMonth = new Date(viewYear, viewMonth + 1, 0).getDate();

        // Células vazias
        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'cal-cell cal-cell--empty';
            calGrid.appendChild(el);
        }

        // Dias do mês
        for (let day = 1; day <= daysInMonth; day++) {
            const date   = new Date(viewYear, viewMonth, day);
            const ymd    = toYMD(date);
            const isPast = ymd < TODAY_STR;
            const isBook = BOOKED_DATES.has(ymd);

            const el = document.createElement('div');
            el.className = 'cal-cell';
            el.dataset.date = ymd;

            // Número do dia
            const num = document.createElement('span');
            num.className = 'cal-cell-num';
            num.textContent = day;
            el.appendChild(num);

            if (isPast) {
                el.classList.add('cal-cell--past');
            } else if (isBook) {
                el.classList.add('cal-cell--booked');
            } else {
                el.classList.add('cal-cell--free');
                // Ponto disponível
                const dot = document.createElement('span');
                dot.className = 'cal-cell-dot';
                el.appendChild(dot);
                el.addEventListener('click', () => onDayClick(date));
            }

            // Aplicar estados de seleção
            applySelectionStyle(el, date);

            calGrid.appendChild(el);
        }
    }

    // ── Aplicar estilo de seleção/intervalo em um cell ────────────────────
    function applySelectionStyle(el, date) {
        if (!el.classList.contains('cal-cell--free')) return;
        el.classList.remove('cal-cell--checkin', 'cal-cell--checkout', 'cal-cell--range');

        if (checkinDate && isSame(date, checkinDate)) {
            el.classList.add('cal-cell--checkin');
        } else if (checkoutDate && isSame(date, checkoutDate)) {
            el.classList.add('cal-cell--checkout');
        } else if (checkinDate && checkoutDate
                   && !isBefore(date, checkinDate)
                   && isBefore(date, checkoutDate)) {
            el.classList.add('cal-cell--range');
        }
    }

    // ── Clique num dia ────────────────────────────────────────────────────
    function onDayClick(date) {
        // Estado: nenhum selecionado ou ambos → começa nova seleção de checkin
        if (!checkinDate || (checkinDate && checkoutDate)) {
            checkinDate  = date;
            checkoutDate = null;
            updateUI();
            return;
        }

        // Já tem checkin, aguardando checkout
        if (isBefore(date, checkinDate) || isSame(date, checkinDate)) {
            // Clicou antes ou no mesmo dia → reinicia
            checkinDate  = date;
            checkoutDate = null;
            updateUI();
            return;
        }

        // Verifica se o intervalo tem datas bloqueadas
        if (rangeHasBooked(checkinDate, date)) {
            alert('Há datas reservadas dentro do período selecionado. Escolha outro intervalo.');
            return;
        }

        checkoutDate = date;
        updateUI();
    }

    // ── Atualizar tudo após mudança de seleção ────────────────────────────
    function updateUI() {
        // Atualiza campos hidden (PHP)
        inputCheckin.value  = checkinDate  ? toYMD(checkinDate)  : '';
        inputCheckout.value = checkoutDate ? toYMD(checkoutDate) : '';

        // Atualiza display visual
        displayCheckin.textContent  = checkinDate  ? toBR(checkinDate)  : '—';
        displayCheckout.textContent = checkoutDate ? toBR(checkoutDate) : '—';

        // Highlight dos campos visuais
        document.getElementById('calCheckinDisplay').classList
            .toggle('cal-date-field--active', !!checkinDate);
        document.getElementById('calCheckoutDisplay').classList
            .toggle('cal-date-field--active', !!checkoutDate);

        // Preview de preço
        if (checkinDate && checkoutDate) {
            const days  = Math.round((checkoutDate - checkinDate) / 86400000);
            const total = days * DAILY_PRICE;
            document.getElementById('previewNights').textContent =
                days + ' noite' + (days !== 1 ? 's' : '');
            document.getElementById('previewTotal').textContent =
                'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
            document.getElementById('previewTotalBold').textContent =
                'R$ ' + total.toLocaleString('pt-BR', {minimumFractionDigits: 2});
            resPreview.style.display = 'block';
            resSubmit.disabled = false;
        } else {
            resPreview.style.display = 'none';
            resSubmit.disabled = true;
        }

        renderCalendar();
    }

    // ── Navegação de meses ────────────────────────────────────────────────
    btnPrev.addEventListener('click', () => {
        viewMonth--;
        if (viewMonth < 0) { viewMonth = 11; viewYear--; }
        renderCalendar();
    });

    btnNext.addEventListener('click', () => {
        viewMonth++;
        if (viewMonth > 11) { viewMonth = 0; viewYear++; }
        renderCalendar();
    });

    // ── Limpar ────────────────────────────────────────────────────────────
    btnClear.addEventListener('click', () => {
        checkinDate  = null;
        checkoutDate = null;
        updateUI();
    });

    // ── Init ──────────────────────────────────────────────────────────────
    renderCalendar();
})();
</script>

<?php require_once('../templates/footer.php'); ?>
