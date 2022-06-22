<header>
    <div uk-sticky="sel-target: .uk-container; cls-active: uk-navbar-sticky">
        <nav class="uk-container uk-padding-small" uk-navbar="mode: click">
            <div class="uk-navbar-left">
                <a class="uk-navbar-item uk-logo" href="#">
                    <img src="assets/images/logo-tsa.png" alt="Logo TSA">
                </a>
            </div>
            <div class="uk-navbar-center uk-visible@l">
                <ul class="uk-navbar-nav">
                    <li><a href="quem-somos">Empresa</a></li>
                    <li><a href="produtos">Produtos</a></li>
                    <li><a href="onde-encontrar">Onde encontrar</a></li>
                    <li><a href="area-tecnica">Área Técnica</a></li>
                    <li><a href="downloads">Downloads</a></li>
                    <li><a href="contato">Contato</a></li>
                </ul>
            </div>
            <div class="uk-navbar-right uk-visible@l">
                <ul class="uk-navbar-nav">
                    <li><a href=""><img src="assets/images/icon-ptbr.png" alt="Português Brasil" title="Português Brasil"></a></li>
                    <li><a href=""><img src="assets/images/icon-eng.png" alt="Inglês - English" title="Inglês"></a></li>
                    <li><a href=""><img src="assets/images/icon-esp.png" alt="Espanhol - Spanish" title="Espanhol"></a></li>
                </ul>
            </div>
            <!-- Canvas -->
            <a class="uk-navbar-toggle uk-hidden@l uk-position-right" uk-navbar-toggle-icon uk-toggle="target: #offcanvas-nav-primary"><span class="off-canvas"></span></a>
        </nav>
    </div>
</header>

<div id="offcanvas-nav-primary" uk-offcanvas="overlay: true; flip: true;">
    <div class="uk-offcanvas-bar uk-flex uk-flex-column">
        <div class="brand uk-text-center">
            <a class="uk-offcanvas-brand" href="#"><img src="assets/images/logo-tsa.png" alt="Logo TSA do Brasil"></a>
        </div>
        <ul class="uk-nav uk-nav-primary uk-padding-small uk-nav-left uk-margin-auto-vertical">
            <li><a href="quem-somos">Empresa</a></li>
            <li><a href="produtos">Produtos</a></li>
            <li><a href="onde-encontrar">Onde encontrar</a></li>
            <li><a href="area-tecnica">Área Técnica</a></li>
            <li><a href="downloads">Downloads</a></li>
            <li><a href="contato">Contato</a></li>
            <li class="uk-nav-divider"></li>
            <li class="idiomas"><a href=""><img src="assets/images/icon-ptbr.png" alt="Português Brasil" title="Português Brasil"></a></li>
            <li class="idiomas"><a href=""><img src="assets/images/icon-eng.png" alt="Inglês - English" title="Inglês"></a></li>
            <li class="idiomas"><a href=""><img src="assets/images/icon-esp.png" alt="Espanhol - Spanish" title="Espanhol"></a></li>
        </ul>
    </div>
</div>