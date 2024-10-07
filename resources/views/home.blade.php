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
				<p class="prepend-top append-0">Thank you for purchasing <strong>Votta - Online voting system.</strong> If you 
					have any questions that are beyond the scope of this help file, please feel free to email 
					via my user page contact form <a href="https://codecanyon.net/user/emo6bruno">here</a>. 
					Thanks so much!</p>
			</div>
		</div><!-- end div .borderTop -->
		
		<hr>
		
		<h2 id="toc" class="alt">Table of Contents</h2>
		<ol class="alpha">
			<li><a href="#specifications">App Specifications</a></li>
			<li><a href="#installation">App Installation</a></li>
			<li><a href="#setup">Inital SetUp</a></li>
			<li><a href="#OnBoarding">OnBoarding</a></li>
			<li><a href="#javascript">JavaScript</a></li>
			<li><a href="#credits">Sources and Credits</a></li>
			
			<li><a href="#phpcode">PHP Code Explanation</a></li>
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
		
		<img src="assets/images/zippedfolders.png" alt="zipped folders" />
		
		<hr>
		
		<h3 id="setup"><strong>B) Initial Setup</strong> - <a href="#toc">top</a></h3>
		<p>Because the app is meant to be for on-boarding new staff for DFCU bank, the assumption is that there is already 
			an existing database of these staff who are yet to be on-boarded.
		</p>

		<p>To add these emails use the super admin email <strong>"admin@email.com"</strong> on the email verification screen.
		</p>

		<p>Add the email and the role you want to the user to have when the successfully onboard</p>
		
		<img src="assets/images/htmlstructure.png" alt="HTML Structure" />
		
		<hr>

		<h3 id="OnBoarding"><strong>C) OnBoarding</strong> - <a href="#toc">top</a></h3>

		<p>After adding the emails of the staff to on board, they can now self on board using the app.</p> 
		
		<p>Below is the process flow;</p> 
		<ol>
			<li>288 x 288 for candidate images</li>
			<li>1100 x 281 for election cover images</li>
		</ol>

		<hr>
		
		<h3 id="javascript"><strong>D) JavaScript</strong> - <a href="#toc">top</a></h3>
		
		<p>This system's main javascript is compiled down to one file <i>app.js</i> by Laravel's build system. You can create custom
			javascript files to modify or support some custom functionality for the application. Several other libraries are used that give the 
			system its behaviour and look and all these libraries come along with their
			<i>.js</i> files. The following are the javascript files included in the main layout 
			(<i>app.blade.php</i> and <i>dashboard.blade.php</i> ) files;
		</p>

		<ul>
			<li>feather.min.js</li>
			<li>aos.js</li>
			<li>glightbox.min.js</li>
			<li>swiper-bundle.min.js</li>
			<li>isotope.pkgd.min.js</li>
			<li>jquery.dataTables.min.js</li>
			<li>jquery-ui.js</li>
			<li>select2.min.js</li>
			<li>perfect-scrollbar.min.js</li>
			<li>votta.js</li>
			<li>custom.js</li>
			<li>dashboard.js</li>
		</ul>
		
		<hr>
		
		<h3 id="credits"><strong>E) Sources and Credits</strong> - <a href="#toc">top</a></h3>
		
		<p>Giving credit to whom credit is due.
		
		<ul>
			<li>Free stock photos from <a href="https:://pexels.com">Pexels</a></li>
			<li><a href="https:://laravel.com">Laravel Framework</a></li>
			<li><a href="https:://getbootstrap.com">Bootstrap Framework</a></li>
			<li><a href="https:://jquery.com">jQuery Framework</a></li>
			<li><a href="https:://datatables.net">DataTables</a></li>
			<li><a href="https:://www.mysql.com">MySQL</a></li>
		</ul>

		<hr>
		
		<h3 id="phpcode"><strong>F) PHP Code Explanation</strong> - <a href="#toc">top</a></h3>
		
		<p>Laravel enforces a MVC(Model View Controller) pattern to application architecture and this pattern has been followed in 
			building of Votta.

			The controllers found at the location below contains the code that performs the actual actions (CRUD) in response to commands or
			requests from the front end.
		</p>
		<pre>votta\app\Http\Controllers</pre>
		
		<ul>
			<li>Properly commented code so that you can easily find out what a specific function is doing</li>
			<li>Appropriate controller names</li>
			<li>Wel structured routes</li>
		</ul>

		<img src="assets/images/code.png" alt="App code" />
		
		<hr>
		
		<h3 id="appfeatures"><strong>G) App features</strong> - <a href="#toc">top</a></h3>
		
		<p>
			These some of the features Votta brings to you;
		</p>
		
		<ul>
			<li>Voter anonimity</li>
			<li>Voter - vote unlinkability</li>
			<li>Bulk user insert</li>
			<li>User management</li>
			<li>User grouping in divisions and sub-divisions</li>
			<li>Bulk divisions/sub-divisions insert</li>
			<li>Create, edit, update, delete elections</li>
			<li>Create, edit, update, delete election posts</li>
			<li>Create, edit, update, delete candidates</li>
			<li>Set start and end dates for elections</li>
			<li>View election results</li>
			<li>Voters cannot vote more than once for the same position</li>
			<li>Transparency - vote timestamp recorded</li>
			<li>Reports</li>
			<li>Well designed UI</li>
		</ul>

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
					<a href="mailto:contact@emtechint.com">contact@emtechint.com</a>
				</li>
			</ul>

		
		
		<hr>
		
		<p>Once again, thank you so much for purchasing this theme. As I said at the beginning, I'd be glad to help you if 
			you have any questions relating to this theme. No guarantees, but I'll do my best to assist. If you have a 
			more general question relating to the php scripts on Codecanyon, you might consider visiting the forums and asking 
			your question.</p> 
		
		<p class="append-bottom alt large"><strong>EM-TECH Global</strong></p>
		<p><a href="#toc">Go To Table of Contents</a></p>
		
		<hr class="space">
	</div><!-- end div .container -->
@endsection
