<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HtmlRendering $rendering
 * @var HttpController $controller
 * @var HttpRequest $request
 * @var HttpRoute $route
 * @var string $CONTROLLER_OUTPUT
 * @var string $content
 * @var FormToken $formToken
 *
 * @var string $panel
 */

use Orpheus\Form\FormToken;
use Orpheus\InputController\HttpController\HttpController;
use Orpheus\InputController\HttpController\HttpRequest;
use Orpheus\InputController\HttpController\HttpRoute;
use Orpheus\Rendering\HtmlRendering;

$rendering->useLayout('page_skeleton');
$rendering->addThemeCssFile('login.css');
$rendering->addThemeJsFile('login.js');

?>
<div id="layoutAuthentication">
	<div id="layoutAuthentication_content">
		<main>
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-7">
						<div class="position-relative">
							
							<div id="PanelLogin" class="card shadow-lg border-0 rounded-lg mt-5 sign-in-panel <?php echo $panel === 'login' ? 'show' : ''; ?>">
								<div class="card-header">
									<h3 class="text-left font-weight-light my-4">
										Connexion
										<i class="fas fa-lock float-right"></i>
									</h3>
								</div>
								<div class="card-body">
									<form method="post">
										<?php
										echo $formToken;
										$rendering->display('reports-bootstrap3');
										?>
										
										<div class="form-group">
											<label class="small mb-1" for="InputLoginEmail">Email</label>
											<input name="login[email]" class="form-control py-4" id="InputLoginEmail" type="email" placeholder="Entrez votre adresse email">
										</div>
										<div class="form-group">
											<label class="small mb-1" for="InputLoginPassword">Mot de passe</label>
											<input name="login[password]" class="form-control py-4" id="InputLoginPassword" type="password" placeholder="Entrez votre mot de passe">
										</div>
										<div class="form-group d-flex align-items-center justify-content-end mt-4 mb-0">
											<?php
											/*
 											justify-content-end
											<a class="small" href="password.html">Forgot Password?</a>
 											*/
											
											?>
											<button class="btn btn-primary" name="submitLogin"><?php _t('login'); ?></button>
										</div>
									</form>
								</div>
								<div class="card-footer text-center">
									<button class="btn btn-link btn-sm" data-toggle-panel="#PanelRegister">Pour s'inscrire, c'est par ici !</button>
								</div>
							</div>
							
							<div id="PanelRegister" class="card shadow-lg border-0 rounded-lg mt-5 sign-in-panel<?php echo $panel === 'register' ? 'show' : ''; ?>">
								<div class="card-header">
									<h3 class="text-left font-weight-light my-4">
										Inscription comme professeur des écoles
										<i class="fas fa-lock float-right"></i>
									</h3>
								</div>
								<div class="card-body">
									<form method="post">
										<?php
										echo $formToken;
										$rendering->display('reports-bootstrap3', ['reportStream' => 'register']);
										?>
										<div class="form-row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="small mb-1" for="InputRegisterFirstName">Votre prénom</label>
													<input name="user[firstname]" class="form-control py-4" id="InputRegisterFirstName" type="text"/>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="small mb-1" for="InputRegisterLastName">Votre nom</label>
													<input name="user[lastname]" class="form-control py-4" id="InputRegisterLastName" type="text"/>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="small mb-1" for="InputRegisterEmail">Email</label>
											<input name="user[email]" class="form-control py-4" id="InputRegisterEmail" type="email"/>
										</div>
										<div class="form-row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="small mb-1" for="InputRegisterPassword">Mot de passe</label>
													<input name="user[password]" class="form-control py-4" id="InputRegisterPassword" type="password"/>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="small mb-1" for="InputRegisterConfirmPassword">Confirmez votre mot de passe</label>
													<input name="user[password_conf]" class="form-control py-4" id="InputRegisterConfirmPassword" type="password"/>
												</div>
											</div>
										</div>
										<div class="form-group d-flex align-items-center justify-content-end mt-4 mb-0">
											<button class="btn btn-primary" name="submitRegister"><?php _t('register'); ?></button>
										</div>
									</form>
								</div>
								<div class="card-footer text-center">
									<button class="btn btn-link btn-sm" data-toggle-panel="#PanelLogin">Vous avez un compte ? Connectez vous !</button>
								</div>
							</div>
						
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
	<div id="layoutAuthentication_footer">
		<footer class="py-4 bg-light mt-auto">
			<div class="container-fluid">
				<div class="d-flex align-items-center justify-content-between small">
					<div class="text-muted">Copyright &copy; <?php _t('app_name'); ?> 2021<?php echo date('Y') !== '2021' ? ' - ' . date('Y') : ''; ?></div>
					<div>Made with ❤ by <a href="https://sowapps.com" target="_blank">Sowapps (Florent Hazard)</a></div>
					<!--					<div>-->
					<!--						<a href="#">Privacy Policy</a>-->
					<!--						&middot;-->
					<!--						<a href="#">Terms &amp; Conditions</a>-->
					<!--					</div>-->
				</div>
			</div>
		</footer>
	</div>
</div>
