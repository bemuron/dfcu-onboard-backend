@extends('layouts.app')

@section('content')
<div class="container">
	
		<div class="center alt"><img src="assets/images/dash_logo.png" alt="logo"></div>
		<h3 class="center alt">&ldquo;DFCU OnBoarding App&rdquo; Documentation by &ldquo;Bruno Emuron&rdquo; v1.0</h3>
		
		<hr>
		
		<h1 class="center">&ldquo;DFCU OnBoarding App&rdquo;</h1>
		
		<div class="borderTop">
			<div class="span-6 colborder info prepend-1">
				<p class="prepend-top">
					<strong>
					Created: 07/10/2024<br>
					By: Bruno Emuron<br>
					Email: <a href="mailto:emo6bruno@gmail.com">emo6bruno@gmail.com</a>
					</strong>
				</p>
			</div><!-- end div .span-6 -->		
	
			<div class="span-12 last">
				<p class="prepend-top append-0">Thank you for using the<strong>DFCU On-Boarding</strong> app. This app
					demonstrates the workings of 3 APIs for dfcu Bank's HR Management System which support new staff onboarding.
					The 3 APIs are: <strong>1. Staff Registration</strong> <strong>2. Staff Retrieval</strong> and <strong>3. Staff Update</strong>
				</p>
			</div>
		</div><!-- end div .borderTop -->
		
		<hr>
		
		<h2 id="toc" class="alt">Table of Contents</h2>
		<ol class="alpha">
			<li><a href="#specifications">App Specifications</a></li>
			<li><a href="#installation">App Installation</a></li>
			<li><a href="#setup">Inital SetUp</a></li>
			<li><a href="#OnBoarding">Staff Registration (OnBoarding)</a></li>
			<li><a href="#retrieval">Staff Retrieval</a></li>
			<li><a href="#update">Staff Update</a></li>
			
			<li><a href="#apiMonitor">Admin Interface</a></li>
			<li><a href="#appfeatures">App features</a></li>
			<li><a href="#support">Support</a></li>
		</ol>

		<hr>
		<h3 id="specifications"><strong>A) App Specifications</strong> - <a href="#toc">top</a></h3>
		<ul>
			<li>Platform - Android</li>
			<li>Built using Java 11</li>
			<li>Target SDK 34 (latest)</li>
			<li>Compatible with Android smartphones and tablets</li>
			<li>Built with Android Studio Koala</li>
			<li>Backend built using PHP - Laravel 8</li>
			<li>MySQL database</li>
		</ul>

		
		<h3 id="installation"><strong>B) App Installation</strong> - <a href="#toc">top</a></h3>
		<p>
			Follow the link below to download the app
		</p>
		
		<!-- <img src="assets/images/zippedfolders.png" alt="zipped folders" /> -->
		
		<hr>
		
		<h3 id="setup"><strong>B) Initial Setup</strong> - <a href="#toc">top</a></h3>
		<p>Because the app is meant to be for on-boarding new staff for DFCU bank, the assumption is that there is already 
			an existing database of these staff who are yet to be on-boarded.
		</p>

		<p>To add these emails use the super admin email <strong>"admin@email.com"</strong> on the email verification screen.
		</p>

		<p>Add the email and the role you want to the user to have when the successfully onboard</p>
		
		<!-- <img src="assets/images/htmlstructure.png" alt="HTML Structure" /> -->
		
		<hr>

		<h3 id="OnBoarding"><strong>C) Staff Registration (OnBoarding)</strong> - <a href="#toc">top</a></h3>

		<p>After adding the emails of the staff to on board, they can now self on board using the app.</p> 
		
		<p>Below is the process flow;</p>
		<ol>
			<li>User enters their email address</li>
			<li>App checks of this email is exists in the staff DB</li>
			<li>If yes, a 10 digit code is generated and sent to that email.</li>
			<li>User enters the 10 digit code sent to their email address.</li>
			<li>App verifies the code to make sure it had not yet expired and if it is the one it sent out.</li>
			<li>If successfull, the user will then be taken to the registration screen where they can enter all necessary details.</li>
			<li>On submit, the user's details are saved and an employee ID is generated for the user.</li>
			<li>The user is then taken to the app home screen</li>
		</ol>
		<p>The app uses a bottom navigation design for its main navigation</p>

		<hr>
		
		<h3 id="retrieval"><strong>D) Staff Retrieval</strong> - <a href="#toc">top</a></h3>
		
		<p>To retieve staff details, simply enter theie employee ID and tap the button to retrieve.
			This initiates a DB search and employee results are returned if found.
		</p>
		
		<hr>
		
		<h3 id="update"><strong>E) Staff Update</strong> - <a href="#toc">top</a></h3>
		
		<p>
			Staff updates can only be carried out by users with the Administrator role.
			The admin can only edit the staff date of birth and the ID images added.
		</p>

		<hr>
		
		<h3 id="apiMonitor"><strong>F) Admin Interface</strong> - <a href="#toc">top</a></h3>
		
		<p><strong>Not yet functional</strong></p>

		<p>This is where the admins can view the performance of the APIs monitorin metrics such as the total number 
			of requests, failed, successful etc.
			This section is currently not functional.
		</p>
		
		<hr>

		<p>
			Your feedback is important so feel free to get in touch. If you want further customization according to your requirements, 
			get in touch so we can discuss the extra cost and your requirements.
		</p>

		<hr>

		<h3 id="support"><strong>H) Support</strong> - <a href="#toc">top</a></h3>

		<p>
			I am happy to provide support to you, simply send mail to the address below.
		</p>

			<ul>
				<li>
					<a href="mailto:emo6bruno@gmail.com">emo6bruno@gmail.com</a>
				</li>
			</ul>

		
		
		<hr>
		
		<p>Thank you again for using and testing the DFCU On Board app by Bruno</p> 
		
		<p class="append-bottom alt large"><strong>Bruno Emuron</strong></p>
		<p><a href="#toc">Go To Table of Contents</a></p>
		
		<hr class="space">
	</div><!-- end div .container -->
@endsection
