    <div class="navbar-menu">
			<nav class="navbar navbar-inverse navbar-shadow" role="navigation">
				<div class="container-fluid">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-menu">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
						<span class="navbar-brand-image"><a href="/"><img src="/images/logo_company.png" /></a></span>
					</div>
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-menu">
						<ul class="nav navbar-nav">
                            <li class="module dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Panel de Control <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/dashboard/licence" >Indicadores de uso de licencias m&#x000F3;viles</a></li>
                                    <li class="dropdown-submenu">
										<a href="#">Indicadores de desempe&#x00148;o</a>
										<ul class="dropdown-menu">
                                            <li><a href="#">Por grupo de oficina</a></li>
                                            <li><a href="#">Por oficina</a></li>
										</ul>
									</li>
                                    <li class="dropdown-submenu">
										<a href="#">Indicadores de actividad por canal</a>
										<ul class="dropdown-menu">
                                            <li><a href="#">Estado de entrega por canal</a></li>
                                            <li><a href="#">Productos devueltos por canal</a></li>
                                            <li><a href="#">Productos devueltos por canal y motivo de devoluci&#243;n</a></li>
                                            <li><a href="#">Estado porcentual de entrega por canal</a></li>
										</ul>
									</li>
                                </ul>
                            </li>
                            <li class="module dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Repartos <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="/deliveries/deliveries">Informe de avances en repartos</a></li>
                                    <li><a href="#">Transferencia de documentos</a></li>
                                    <li><a href="#">Redespacho de documentos</a></li>
                                    <li><a href="#">Rendici&#243;n de documentos</a></li>
                                    <li><a href="#">Eliminar documentos</a></li>
                                    <li><a href="#">Notificaci&#243;n de reparto</a></li>
                                </ul>
                            </li>
                            <li class="module dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Mantenedores<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li>
										<a href="/maintainers/office-groups">Grupos de oficinas</a>
									</li>
                                    <li>
										<a href="/maintainers/offices">Oficinas</a>
									</li>
                                    <li>
										<a href="/maintainers/vehicles-groups">Groupo de Veh&#237;culos</a>
									</li>
                                    <li>
										<a href="/maintainers/vehicles/">Veh&#237;culos</a>
									</li>
                                    <li class="dropdown-submenu">
										<a href="/maintainers/profiles/">Perfiles</a>
                                        <ul class="dropdown-menu">
                                            <li><a href="/maintainers/users/">Usuarios</a></li>
										</ul>
									</li>
                                    <li>
										<a href="/maintainers/devices/">Android</a>
									</li>
                                    <li>
										<a href="/maintainers/status/">Motivos de devoluci&#243;n</a>
									</li>
                                    <li>
										<a href="/maintainers/sellers/">Vendedores</a>
									</li>
                                    <li>
										<a href="/maintainers/employees/">Empleados</a>
									</li>
                                </ul>
                            </li>
                            <li class="module dropdown">
                                <a href="/upload/documents" role="button" aria-haspopup="true" aria-expanded="false">Documentos</a>
                            </li>
                            <li class="module dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reportes<span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li>
										<a href="/reports/summary/">Resumen del SDL</a>
									</li>
                                    <li>
										<a href="/reports/performance-plate">Desempe&#241;o Patente</a>
									</li>
                                    <li>
										<a href="/reports/performance-driver">Desempe&#241;o Conductor</a>
									</li>
                                </ul>
                            </li>
                        </ul>
						<ul class="nav navbar-nav navbar-right">
                            <!-- Check later -->
                            <li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyphicon glyphicon-th-large"></span> <span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
                                    <li class="module"><a href="/profile">Mi Perfil</a></li>
									<li class="divider"></li>
									<li class="logout">
                                        <a href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                            Salir
                                        </a>
                                        <form id="logout-form" action="/logout" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</div>
		<div class="navbar-xs">
			<div class="navbar-submenu">
				<nav class="navbar" role="navigation">
					<div class="container-fluid">
						<div class="navbar-header">
							<div class="navbar-brand-small overflow"><span class="title"><?php //echo $active_module["description"]; ?></span> &#8212; <span class="sub-title"><?php //echo $active_function["description"]; ?></span></div>
						</div>
						<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-menu-submenu">
							<ul class="nav navbar-nav navbar-right"></ul>
						</div>
					</div>
				</nav>
			</div>
		</div>
