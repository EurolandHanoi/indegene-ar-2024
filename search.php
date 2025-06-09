<?php
// === Search Logic + Pagination ===

$searchTerm = isset($_GET['q']) ? strtolower(trim($_GET['q'])) : '';
$resultsPerPage = 5; // Change results per page here
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$results = [];

function highlightTerms($text, $term) {
	return preg_replace("/(" . preg_quote($term, '/') . ")/i", "<mark>$1</mark>", $text);
}

// Search HTML files
$htmlFiles = glob("*.html");

foreach ($htmlFiles as $file) {
	$content = file_get_contents($file);
	$text = strip_tags($content);
	if ($searchTerm !== '' && stripos($text, $searchTerm) !== false) {
		preg_match("/<p[^>]*>.*?" . preg_quote($searchTerm, '/') . ".*?<\/p>/i", $content, $matches);
		$snippet = isset($matches[0]) ? strip_tags($matches[0]) : substr($text, stripos($text, $searchTerm), 200);
		$results[] = [
			'type' => 'html',
			'title' => ucfirst(basename($file, ".html")),
			'url' => $file,
			'snippet' => highlightTerms($snippet, $searchTerm)
		];
	}
}

// Search PDF filenames
$pdfFiles = glob("pdf-files/*.pdf");

foreach ($pdfFiles as $file) {
	$filename = basename($file);
	if ($searchTerm !== '' && stripos($filename, $searchTerm) !== false) {
		$results[] = [
			'type' => 'pdf',
			'title' => $filename,
			'url' => $file,
			'snippet' => 'Matching PDF file: ' . highlightTerms($filename, $searchTerm)
		];
	}
}

// Pagination calculations
$totalResults = count($results);
$totalPages = ceil($totalResults / $resultsPerPage);
$page = min($page, $totalPages > 0 ? $totalPages : 1);
$startIndex = ($page - 1) * $resultsPerPage;
$resultsForPage = array_slice($results, $startIndex, $resultsPerPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Board of Directors</title>
	<link rel="icon" href="images/favicon.svg" type="image/gif" sizes="32x32">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css" />
	<link rel="stylesheet" href="css/owl.theme.default.min.css" type="text/css" />
	<link rel="stylesheet" href="css/animate.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="css/style.css" type="text/css" rel="stylesheet">
	<link href="css/responsive.css" type="text/css" rel="stylesheet">
	<style>
/* Container for search results */
.search-results {
	max-width: 720px;
	margin: 0 auto 3rem;
	font-family: Arial, sans-serif;
	color: #202124;
	line-height: 1.4;
}

/* Each individual result */
.result {
	margin-bottom: 2.5rem;
}

/* The link (title) */
.result a {
	font-size: 18px;
	color: #1a0dab;
	text-decoration: none;
	line-height: 1.3;
	display: inline-block;
	max-width: 100%;
	cursor: pointer;
	transition: all 0.3s ease;
	word-break: break-word;
}

.result a:hover {
	text-decoration: underline;
}

/* The URL display */
.result .url {
	font-size: 14px;
	color: #006621;
	margin-bottom: 4px;
	display: block;
	word-break: break-all;
}

/* The snippet text */
.result p.snippet {
	font-size: 14px;
	color: #4d5156;
	margin-top: 0;
	margin-bottom: 6px;
}

/* PDF download link styling */
.result a.pdf-download {
	font-size: 13px;
	color: #c5221f;
	text-decoration: none;
	font-weight: 600;
	border: 1px solid #c5221f;
	padding: 3px 8px;
	border-radius: 3px;
	display: inline-block;
	transition: background-color 0.3s ease;
}

.result a.pdf-download:hover {
	background-color: #c5221f;
	color: white;
}

/* Responsive */
@media (max-width: 600px) {
	.search-results {
		padding: 0 1rem;
	}
}
.pagination {
	display: flex;
	justify-content: center;
	align-items: center;
	margin-top: 20px;
	gap: 8px;
	flex-wrap: wrap;
	font-family: Arial, sans-serif;
}

.pagination button {
	border: 1px solid #dadce0;
	background: white;
	color: #1a0dab;
	padding: 6px 12px;
	cursor: pointer;
	font-size: 14px;
	border-radius: 4px;
	min-width: 36px;
	text-align: center;
	transition: background-color 0.3s ease;
}

.pagination button:hover:not(.disabled):not(.active) {
	background-color: #f8f9fa;
}

.pagination button.disabled {
	color: #9aa0a6;
	cursor: default;
	border-color: #f1f3f4;
}

.pagination button.active {
	background-color: #1a73e8;
	color: white;
	border-color: #1a73e8;
	cursor: default;
	font-weight: 600;
}

.pagination span {
	padding: 6px 10px;
	user-select: none;
	color: #5f6368;
}

</style>
</head>
<body class="homePage searchBg">	
	<header id="header">
		<div class="container">
			<div class="row">
				<div class="col-xl-6 col-sm-8 col-6">
					<div class="leftlogo-warp">
						<div class="h-logo">
							<a href="index.html"><img src="images/logo.svg"></a>
						</div>
					</div>
				</div>
				<div class="col-xl-6 col-sm-4 col-6">
					<div class="right-menu">
						<h6>Annual Report FY 2024-25</h6>
						<!-- Search Icon and Input -->
						<form action="search.php" method="get" class="search-form">
							<div class="search-wrapper">
								<img src="images/search-white.svg" class="search-icon" alt="Search" />
								<input type="text" name="q" class="search-input" placeholder="Search..." />
							</div>
						</form>
						<div class="ham-icon menu nav-icon3">
							<div class="menutxt">
								<span></span>
								<span></span>
								<span></span>
								<span></span>
							</div>
						</div>
						
						<div class="ham-icon1">
							<img src="images/menu.svg">
						</div>

					</div>

				</div>
			</div>
		</div>
	</div>
</header>

<div class="my-sidenav1">
	<div class="container">
		<div class="row">
			<div class="col-xl-4">
				<div class="top-nav">
					<ul class="nav nav-pills">
						<li class="nav-item" role="presentation">
							<a  class="nav-link active" id="co-tab" data-bs-toggle="tab" data-bs-target="#co" type="button" role="tab" aria-controls="home" aria-selected="true">AI: A Generational Opportunity</a>
						</li>
						<li class="nav-item" role="presentation">
							<a  class="nav-link" id="pe-tab" data-bs-toggle="tab" data-bs-target="#pe" type="button" role="tab" aria-controls="home" aria-selected="true">Company Overview</a>
						</li>
						<li class="nav-item" role="presentation">
							<a  class="nav-link" id="scl-tab" data-bs-toggle="tab" data-bs-target="#scl" type="button" role="tab" aria-controls="home" aria-selected="true">Strategic Overview</a>
						</li>
						<li class="nav-item" role="presentation">
							<a  class="nav-link" id="gov-tab" data-bs-toggle="tab" data-bs-target="#gov" type="button" role="tab" aria-controls="home" aria-selected="true">People and Community</a>
						</li>
						<li class="nav-item" role="presentation">
							<a  class="nav-link" id="dd-tab" data-bs-toggle="tab" data-bs-target="#dd" type="button" role="tab" aria-controls="home" aria-selected="true">Statutory Reports</a>
						</li>

						<li class="nav-item" role="presentation">
							<a  class="nav-link" id="sr-tab" data-bs-toggle="tab" data-bs-target="#sr" type="button" role="tab" aria-controls="home" aria-selected="true">Financial Statements</a>
						</li>

					</ul>
				</div><!--top-nav-->
			</div><!--col-md-3-->

			<div class="col-xl-6">
				<div class="tab-content top-nav-content">
					<div class="tab-pane active" id="co" role="tabpanel" aria-labelledby="co-tab">
						<ul class="sub-menu">
							<li><a href="ai-a-generational-opportunity.html">AI: A Generational Opportunity</a>
							</li>
						</ul>
					</div><!--#co-->
					<div class="tab-pane" id="pe" role="tabpanel" aria-labelledby="pe-tab">
						<ul class="sub-menu">
							<li><a href="about-indegene.html">About Indegene</a></li>
							<li><a href="our-journey.html">Our Journey</a></li>
							<li><a href="key-differentiators.html">Key Differentiators</a></li>
							<li><a href="our-offerings.html">Our Offerings</a></li> 

						</ul>
					</div><!--#em-->
					<div class="tab-pane" id="scl" role="tabpanel" aria-labelledby="scl-tab">
						<ul class="sub-menu">               
							<li><a href="message-from-chairman-and-ceo.html">Message from the Chairman and CEO</a></li>
							<li><a href="performance-highlights.html">Performance Highlights</a></li>
							<li><a href="awards-and-recognition.html">Awards and Recognition</a></li>
							<li><a href="our-ai-first-approach.html">Our AI-First Approach</a></li>
						</ul>
					</div><!--#kf-->
					<div  class="tab-pane" id="gov" role="tabpanel" aria-labelledby="gov-tab">
						<ul class="sub-menu">
							<li><a href="people-excellence.html">People Excellence</a></li>
							<li><a href="indegene-approach-to-esg.html">Indegene's Approach to ESG</a></li>
							<li><a href="board-of-directors.html">Board of Directors</a></li>
<li><a href="corporate-information.html">Corporate Information</a></li>
						</ul>
					</div><!--#mda-->

					<div  class="tab-pane" id="dd" role="tabpanel" aria-labelledby="dd-tab">
						<ul class="sub-menu">
							<li class="d-pdf"><a href="pdf/Management Discussion and Analysis.pdf" target="_blank">Management Discussion and Analysis <span>Download PDF</span></a></li>
							<li class="d-pdf"><a href="pdf/Notice.pdf" target="_blank">Notice <span>Download PDF</span></a></li>
							<li class="d-pdf"><a href="pdf/Board’s Report.pdf" target="_blank">Board's Report<span>Download PDF</span></a></li>
							<li class="d-pdf"><a href="pdf/Corporate Governance Report.pdf" target="_blank">Corporate Governance Report<span>Download PDF</span></a></li>
							<li class="d-pdf"><a href="pdf/Business Responsibility and Sustainability Report.pdf" target="_blank">Business Responsibility and Sustainability Report<span>Download PDF</span></a></li>
						</ul>
					</div><!--#dd-->

					<div  class="tab-pane" id="sr" role="tabpanel" aria-labelledby="sr-tab">
						<ul class="sub-menu">
							<li class="d-pdf"><a href="pdf/Standalone Financial Statements.pdf" target="_blank">Standalone Financial Statements<span>Download PDF</span></a></li>
							<li class="d-pdf"><a href="pdf/Consolidated Financial statements.pdf" target="_blank">Consolidated Financial Statements<span>Download PDF</span></a></li>
						</ul>
					</div><!--#sr-->

				</div><!--tab-content-->
			</div><!--col-md-3-->

		</div>
	</div>


</div>  

<div class="my-sidenav">
	<div class="cross-btn1">&times;</div>
	<ul>
		<li class="menu-drop"><a href="javascript:void(0)">AI: A Generational Opportunity</a>
			<ul class="submenu">
				<li><a href="ai-a-generational-opportunity.html">AI: A Generational Opportunity</a>
				</li>
			</ul>
		</li>

		<li class="menu-drop"><a href="javascript:void(0)">Company Overview</a>
			<ul class="submenu">
				<li><a href="about-indegene.html">About Indegene</a></li>
				<li><a href="our-journey.html">Our Journey</a></li>
				<li><a href="key-differentiators.html">Key Differentiators</a></li>
				<li><a href="our-offerings.html">Our Offerings</a></li> 
			</ul>
		</li>
		<li class="menu-drop"><a href="javascript:void(0)">Strategic Overview</a>
			<ul class="submenu">
				<li><a href="message-from-chairman-and-ceo.html">Message from the Chairman and CEO</a></li>
				<li><a href="performance-highlights.html">Performance Highlights</a></li>
				<li><a href="awards-and-recognition.html">Awards and Recognition</a></li>
				<li><a href="our-ai-first-approach.html">Our AI-First Approach</a></li>
			</ul>
		</li>

		<li class="menu-drop"><a href="javascript:void(0)">People and Community</a>
			<ul class="submenu">
				<li><a href="people-excellence.html">People Excellence</a></li>
				<li><a href="indegene-approach-to-esg.html">Indegene's Approach to ESG</a></li>
				<li><a href="board-of-directors.html">Board of Directors</a></li>
<li><a href="corporate-information.html">Corporate Information</a></li>
			</ul>
		</li>

		<li class="menu-drop"><a href="javascript:void(0)">Statutory Reports</a>
			<ul class="submenu">
				<li class="d-pdf"><a href="pdf/Management Discussion and Analysis.pdf" target="_blank">Management Discussion and Analysis <span>Download PDF</span></a></li>
				<li class="d-pdf"><a href="pdf/Notice.pdf" target="_blank">Notice <span>Download PDF</span></a></li>
				<li class="d-pdf"><a href="pdf/Board’s Report.pdf" target="_blank">Board's Report<span>Download PDF</span></a></li>
				<li class="d-pdf"><a href="pdf/Corporate Governance Report.pdf" target="_blank">Corporate Governance Report<span>Download PDF</span></a></li>
				<li class="d-pdf"><a href="pdf/Business Responsibility and Sustainability Report.pdf" target="_blank">Business Responsibility and Sustainability Report<span>Download PDF</span></a></li>
			</ul>
		</li>

		<li class="menu-drop"><a href="javascript:void(0)">Financial Statements</a>
			<ul class="submenu">
				<li class="d-pdf"><a href="pdf/Standalone Financial Statements.pdf" target="_blank">Standalone Financial Statements<span>Download PDF</span></a></li>
				<li class="d-pdf"><a href="pdf/Consolidated Financial statements.pdf" target="_blank">Consolidated Financial Statements<span>Download PDF</span></a></li>
			</ul>
		</li>
	</ul>
</div>
<div id="wrapper">		
	<section class="insideBanner fullHeads">
		<div class="container">
			<div class="row">
				<div class="col-xl-12">
					<div class="siteMap gapR">
						<a href="">Home / </a>
						<a href="">Search</a>
					</div>

				</div>

			</div>
		</div>
	</section>

	<body>
		<section class="insidePage">
			<div class="searchSec">
				<div class="container">
					<div class="row">
						<div class="col-xl-12">
							<h1>Search Results for "<?= htmlspecialchars($searchTerm) ?>"</h1>

							<?php if (empty($resultsForPage)): ?>
								<p>No results found.</p>
							<?php else: ?>
								<?php foreach ($resultsForPage as $res): ?>
									<div class="result">
										<a href="<?= $res['url'] ?>" target="_blank" rel="noopener noreferrer">
											<?= htmlspecialchars($res['title']) ?>
										</a>
										<p>
											<?= $res['snippet'] ?>
										</p>
										<?php if ($res['type'] == 'pdf'): ?>
											<a class="pdf-download" href="<?= $res['url'] ?>" download>Download PDF</a>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>

							<div class="pagination" id="pagination"></div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<footer>
			<div class="footer-top">
				<div class="container">
					<div class="row">
						<div class="col-xl-8">
							<h4 class="downloadImg"><a href="pdf/Indegene AR 2024-25_29052025 Final.pdf" target="_blank">Annual Report FY 2024-25 <img src="images/download.svg"></a></h4>
							<ul class="footer-link">
								<li><a href="pdf/Management Discussion and Analysis.pdf" target="_blank">Management Discussion and Analysis</a></li>
								<li><a href="pdf/Board’s Report.pdf" target="_blank">Board’s Report</a></li>
								<li><a href="pdf/Corporate Governance Report.pdf" target="_blank">Corporate Governance Report</a></li>
								<li><a href="pdf/Business Responsibility and Sustainability Report.pdf" target="_blank">Business Responsibility and Sustainability Report</a></li>
							</ul>
							<ul class="footer-link bd-none">
								<li><a href="pdf/Standalone Financial Statements.pdf" target="_blank">Standalone Financial Statements</a></li>
								<li><a href="pdf/Consolidated Financial statements.pdf" target="_blank">Consolidated Financial Statements</a></li>
							</ul>
						</div>
						<div class="col-xl-3 offset-xl-1">
							<div class="footSocial">
								<p>Follow Us</p>
								<ul>
									<li><a href="https://in.linkedin.com/company/indegene" target="_blank"><img src="images/linkdin-icon.svg"></a></li>
									<li><a href="https://www.youtube.com/@Indegeneinc" target="_blank"><img src="images/yt-icon.svg"></a></li>
									<li><a href="https://www.instagram.com/indegene.official/" target="_blank"><img src="images/insta-icon.svg"></a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="copyRightBg">
				<div class="container">
					<div class="row">
						<div class="col-xl-12">
							<div class="footerBot">
								<div class="copyrightTxt">
									<p>© 2025 Indegene. All Rights Reserved.</p>
								</div>
								<div class="policyLinks">
									<ul>
										<li><a href="https://www.indegene.com/privacy-policy" target="_blank">Privacy Policy</a></li>
										<li><a href="https://www.indegene.com/corporate-social-responsibility-policy" target="_blank">CSR Policy</a></li>
										<li><a href="https://www.indegene.com/policies" target="_blank">Other Policies</a></li>
										<li><a href="https://app.convercent.com/en-US/LandingPage/bc3837ce-c21f-ed11-a98f-000d3ab9f296?_=1666083084096" target="_blank">Indegene Speak Up</a></li>
										<li><a href="https://www.indegene.com/indegene-cookie-policy" target="_blank">Cookies</a></li>
									</ul>
								</div>
								<div class="rdxDev">
									<p>Designed & Developed by  <a href="https://www.rdxsolutions.in/" target="_blank">RDX Digital</a></p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</div>
	<script src="js/jquery-3.7.1.min.js"></script> 
	<script src="js/bootstrap.bundle.min.js"></script> 
	<script src="js/owl.carousel.min.js"></script> 
	<script src="js/jquery.waypoints.min.js"></script> 
	<script src="js/jquery.counterup.js"></script> 
	<script src="js/custom.js"></script> 
	<script src="js/wow.min.js"></script>
	<script src="js/script.js"></script>

	<script src="js/gsap.min.js"></script>
	<script src="js/ScrollTrigger.min.js"></script>
<!-- <script src="js/ScrollSmoother.min.js"></script>
<script src='js/ScrollToPlugin.min.js'></script> -->
<script src="js/splittext.min.js"></script> 
<script src="js/app.js"></script> 
<script>
	$(document).ready(function(){
		$('.search-icon').click(function() {
			$('.search-wrapper').toggleClass('active');
			$('.search-input').focus();
		});
	});
</script>

<script>
	jQuery(document).ready(function( $ ) {
		$('.counter').counterUp({
			delay: 10,
			time: 1500,
		});
		$('.backTop').bind('click', function(e) {

			e.preventDefault();

			$('body,html').animate({scrollTop:0},800);  

		});
	});
	new WOW().init();
</script>

<script>
	$(document).ready(function(){
		$('.nav-icon3').click(function(){
			$(this).toggleClass('open');
		});
		$(".mega-dropdown a").click(function () {
			$(".nav-icon3").removeClass("open");
		});

	});
</script>
<script>
	$(document).ready(function(){
		$('#tab_selector').on('change', function () {
			const selectedIndex = $(this).val();
			const tabTrigger = new bootstrap.Tab($('#myTab button').eq(selectedIndex)[0]);
			tabTrigger.show();
		});
	});

</script>


<script>
	const slider = document.getElementById('slider1');
	const resizable = document.getElementById('resizable');
	const container = document.getElementById('sliderContainer');

	let isDragging = false;
	let startX = 0;
	let startY = 0;

	const moveSlider = (x) => {
		const rect = container.getBoundingClientRect();
		let offsetX = x - rect.left;
		offsetX = Math.max(0, Math.min(offsetX, rect.width));
		const percent = (offsetX / rect.width) * 100;
		slider.style.left = percent + '%';
		resizable.style.width = percent + '%';
	};

// Desktop
	slider.addEventListener('mousedown', (e) => {
		isDragging = true;
	});

	window.addEventListener('mouseup', () => isDragging = false);

	window.addEventListener('mousemove', (e) => {
		if (!isDragging) return;
		moveSlider(e.clientX);
	});

// Mobile
	slider.addEventListener('touchstart', (e) => {
		isDragging = true;
		startX = e.touches[0].clientX;
		startY = e.touches[0].clientY;
	}, { passive: true });

	window.addEventListener('touchend', () => isDragging = false);

	window.addEventListener('touchmove', (e) => {
		if (!isDragging) return;

		const deltaX = e.touches[0].clientX - startX;
		const deltaY = e.touches[0].clientY - startY;

  // Only prevent default if horizontal drag is more significant than vertical
		if (Math.abs(deltaX) > Math.abs(deltaY)) {
			moveSlider(e.touches[0].clientX);
    e.preventDefault(); // Prevent horizontal scroll only
  }
}, { passive: false });
</script>
<script>
	const totalPages = <?= $totalPages ?>;
	let currentPage = <?= $page ?>;

	function renderPagination(totalPages, currentPage) {
		const container = document.getElementById('pagination');
		container.innerHTML = '';

  if (totalPages <= 1) return; // No pagination if only one page

  function createBtn(text, disabled = false, active = false, page = null) {
  	const btn = document.createElement('button');
  	btn.textContent = text;
  	if (disabled) btn.classList.add('disabled');
  	if (active) btn.classList.add('active');
  	if (!disabled && !active && page !== null) {
  		btn.addEventListener('click', () => {
        // Reload page with q and page params
  			const urlParams = new URLSearchParams(window.location.search);
  			urlParams.set('page', page);
  			window.location.search = urlParams.toString();
  		});
  	}
  	return btn;
  }

  // Previous button
  container.appendChild(createBtn('←', currentPage === 1, false, currentPage - 1));

  // Show all pages if totalPages <= 7, else show current ±2 pages with first and last and dots
  if (totalPages <= 7) {
  	for(let i = 1; i <= totalPages; i++) {
  		container.appendChild(createBtn(i, false, i === currentPage, i));
  	}
  } else {
  	if (currentPage > 3) {
  		container.appendChild(createBtn(1, false, false, 1));
  		if (currentPage > 4) {
  			const dots = document.createElement('span');
  			dots.textContent = '...';
  			dots.style.padding = '0 6px';
  			container.appendChild(dots);
  		}
  	}
  	let start = Math.max(2, currentPage - 2);
  	let end = Math.min(totalPages - 1, currentPage + 2);

  	for(let i = start; i <= end; i++) {
  		container.appendChild(createBtn(i, false, i === currentPage, i));
  	}

  	if (currentPage < totalPages - 2) {
  		if (currentPage < totalPages - 3) {
  			const dots = document.createElement('span');
  			dots.textContent = '...';
  			dots.style.padding = '0 6px';
  			container.appendChild(dots);
  		}
  		container.appendChild(createBtn(totalPages, false, false, totalPages));
  	}
  }

  // Next button
  container.appendChild(createBtn('→', currentPage === totalPages, false, currentPage + 1));
}

renderPagination(totalPages, currentPage);
</script>
</body>
</html>
