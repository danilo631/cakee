<?php include('../src/views/templates/header.php'); ?>

<!-- Toldo -->
<div class="toldo">
    <img src="./imagens/toldo_base site div.png" alt="Toldo decorativo do site">
</div>

<head>  <link rel="stylesheet" href="/assets/css/style.css"> </head>

<!-- Conteúdo Principal -->
<main>
    <h1 id="title">Nossos serviços</h1>

    <!-- Grade de Serviços -->
    <section class="servicos-grid">
        <!-- Kit Festas -->
        <a href="kit-festa.php" class="servico-item">
            <h2 class="servico-titulo">Kit Festas</h2>
            <div class="servico-imagem">
                <img src="./imagens/meu_servico_imagem_1.jpg" alt="Imagem de Kit Festas">
            </div>
        </a>

        <!-- Doces -->
        <a href="doces.php" class="servico-item">
            <h2 class="servico-titulo">Doces</h2>
            <div class="servico-imagem">
                <img src="./imagens/meu_servico_imagem_2.png" alt="Imagem de Doces">
            </div>
        </a>

        <!-- Bolos -->
        <a href="bolos.php" class="servico-item">
            <h2 class="servico-titulo">Bolos</h2>
            <div class="servico-imagem">
                <img src="./imagens/meu_servico_imagem_3.png" alt="Imagem de Bolos">
            </div>
        </a>
    </section>
</main>

<?php include('../src/views/templates/footer.php'); ?>
