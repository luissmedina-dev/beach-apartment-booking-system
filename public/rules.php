<?php
    require_once("../templates/header.php");
    require_once("../templates/navbar.php");
?>

<section class="rules-page">

    <div class="rules-header">
        <span class="section-tag">Antes de reservar</span>
        <h1>Regras de utilização</h1>
        <p>Para garantir uma estadia agradável a todos, pedimos que leia e respeite as regras do apartamento e do condomínio.</p>
    </div>

    <div class="rules-grid">

        <article class="rule-card">
            <div class="rule-icon">🌙</div>
            <h2>Silêncio noturno</h2>
            <p>Respeite o horário de silêncio entre 22h e 8h, evitando sons altos e atividades que perturbem outros moradores.</p>
        </article>

        <article class="rule-card">
            <div class="rule-icon">👥</div>
            <h2>Capacidade máxima</h2>
            <p>A ocupação deve respeitar o limite de até 6 hóspedes. Exceder a capacidade sem autorização prévia não é permitido.</p>
        </article>

        <article class="rule-card">
            <div class="rule-icon">🏠</div>
            <h2>Conservação do imóvel</h2>
            <p>Use os móveis, eletrodomésticos e utensílios com cuidado. O imóvel deve ser entregue nas mesmas condições da chegada.</p>
        </article>

        <article class="rule-card">
            <div class="rule-icon">⚠️</div>
            <h2>Danos e quebras</h2>
            <p>Comunique imediatamente qualquer dano causado. Custos de reparo ou substituição poderão ser cobrados do responsável.</p>
        </article>

        <article class="rule-card">
            <div class="rule-icon">🧹</div>
            <h2>Limpeza e organização</h2>
            <p>Mantenha o imóvel limpo e organizado durante a estadia. Descarte o lixo corretamente nos locais indicados.</p>
        </article>

        <article class="rule-card">
            <div class="rule-icon">🐾</div>
            <h2>Animais de estimação</h2>
            <p>Pets são permitidos mediante autorização prévia dos proprietários e conforme as regras do condomínio.</p>
        </article>

        <article class="rule-card">
            <div class="rule-icon">🏢</div>
            <h2>Áreas comuns</h2>
            <p>As áreas comuns do condomínio devem ser utilizadas conforme as normas internas da administração condominial.</p>
        </article>

        <article class="rule-card">
            <div class="rule-icon">🚗</div>
            <h2>Estacionamento</h2>
            <p>A vaga disponibilizada é de uso exclusivo dos hóspedes autorizados, conforme as orientações do condomínio.</p>
        </article>

        <article class="rule-card">
            <div class="rule-icon">📅</div>
            <h2>Cancelamentos</h2>
            <p>Alterações ou cancelamentos devem ser solicitados com antecedência e estão sujeitos à política dos proprietários.</p>
        </article>

        <article class="rule-card rule-card--highlight">
            <div class="rule-icon">✅</div>
            <h2>Aceitação dos termos</h2>
            <p>Ao realizar a reserva, o hóspede declara estar ciente e de acordo com todas as regras do imóvel e do condomínio.</p>
        </article>

    </div>

    <div class="rules-cta">
        <p>Está de acordo? Verifique a disponibilidade e garanta sua estadia.</p>
        <a href="../public/availability.php" class="rules-cta-btn">Ver disponibilidade</a>
    </div>

</section>

<?php require_once("../templates/footer.php"); ?>
