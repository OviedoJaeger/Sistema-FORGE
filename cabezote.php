 <header class="main-header">

    <!--=========================================
    LOGOTIPO
    ==========================================-->
    <a href="inicio" class="logo bg-navy color-palette">


        <!-- logo mini -->
        <span class="logo-mini">

            <img src="vistas\img\plantilla\icono-mini.png" class="
            img-responsive" style="padding:10px">

        </span>
        <!-- logo normal -->
        <span class="logo-lg">

            <img src="vistas\img\plantilla\logo-lineal.png" class="
            img-responsive" width="100" height="80" hspace=50 vspace=5>

        </span>
    </a>

    <!--=========================================
    BARRA DE NAVEGACIÓN
    ==========================================-->
    <nav class="navbar navbar-static-top bg-navy color-palette" role="navigation">
        <!----------BOTON DE NAVEGACION------->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <!----------PERFIL DE USUARIO------->
        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">
                <li class="dropdown user user-menu">

                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">

                    <?php 
                    
                    if ($_SESSION["foto"] != "") {
                        
                        echo '<img src="'.$_SESSION["foto"].'" class="user-image">';

                    } else {
                        
                        echo '<img src="vistas/img/usuarios/default/anonimo.png" class="user-image">';

                    }
                    
                    ?>

                        
                        <span class="hidden-xs"><?php echo $_SESSION["nombre"]; ?></span>

                    </a>

                    <!----------DropDown Toggle------->

                    <ul class="dropdown-menu">

                        <li class="user-body">
    
                            <div class="pull-right">

                                <a href="salir" class="btn btn-default btn-flat">Salir</a>

                            </div>
    
                        </li>

                    </ul>

                </li>

            </ul>

        </div>
        

    </nav>






</header>
