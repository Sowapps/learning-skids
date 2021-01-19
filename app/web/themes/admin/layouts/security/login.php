<?php
/**
 * @author Florent HAZARD <f.hazard@sowapps.com>
 *
 * @var HTMLRendering $rendering
 * @var HTTPController $Controller
 * @var HTTPRequest $Request
 * @var HTTPRoute $Route
 * @var string $CONTROLLER_OUTPUT
 * @var string $Content
 */

use Orpheus\InputController\HTTPController\HTTPController;
use Orpheus\InputController\HTTPController\HTTPRequest;
use Orpheus\InputController\HTTPController\HTTPRoute;
use Orpheus\Rendering\HTMLRendering;

$rendering->useLayout('page_skeleton');
$rendering->addThemeCssFile('sign-in.css');
$rendering->addThemeJsFile('sign-in.js');

?>
<div id="layoutAuthentication">
	<div id="layoutAuthentication_content">
		<main>
			<div class="container">
				<div class="row justify-content-center">
					<div class="col-lg-7">
						<div class="position-relative">
							
							<div id="PanelLogin" class="card shadow-lg border-0 rounded-lg mt-5 sign-in-panel show">
								<div class="card-header">
									<h3 class="text-left font-weight-light my-4">
										Connexion
										<i class="fas fa-lock float-right"></i>
									</h3>
								</div>
								<div class="card-body">
									<form method="post">
										<div class="form-group">
											<label class="small mb-1" for="InputLoginEmail">Email</label>
											<input name="login[email]" class="form-control py-4" id="InputLoginEmail" type="email" placeholder="Entrez votre adresse email">
										</div>
										<div class="form-group">
											<label class="small mb-1" for="InputLoginPassword">Mot de passe</label>
											<input name="login[email]" class="form-control py-4" id="InputLoginPassword" type="password" placeholder="Entrez votre mot de passe">
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
							
							<div id="PanelRegister" class="card shadow-lg border-0 rounded-lg mt-5 sign-in-panel">
								<div class="card-header">
									<h3 class="text-left font-weight-light my-4">
										Inscription comme professeur des écoles
										<i class="fas fa-lock float-right"></i>
									</h3>
								</div>
								<div class="card-body">
									<form>
										<div class="form-row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="small mb-1" for="inputFirstName">First Name</label>
													<input class="form-control py-4" id="inputFirstName" type="text" placeholder="Enter first name"/>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="small mb-1" for="inputLastName">Last Name</label>
													<input class="form-control py-4" id="inputLastName" type="text" placeholder="Enter last name"/>
												</div>
											</div>
										</div>
										<div class="form-group">
											<label class="small mb-1" for="InputLoginEmail">Email</label>
											<input class="form-control py-4" id="inputEmailAddress" type="email" aria-describedby="emailHelp" placeholder="Enter email address"/>
										</div>
										<div class="form-row">
											<div class="col-md-6">
												<div class="form-group">
													<label class="small mb-1" for="InputLoginPassword">Password</label>
													<input class="form-control py-4" id="inputPassword" type="password" placeholder="Enter password"/>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label class="small mb-1" for="inputConfirmPassword">Confirm Password</label>
													<input class="form-control py-4" id="inputConfirmPassword" type="password" placeholder="Confirm password"/>
												</div>
											</div>
										</div>
										<div class="form-group mt-4 mb-0"><a class="btn btn-primary btn-block" href="#Register">Create Account</a></div>
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
