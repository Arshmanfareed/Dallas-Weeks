@extends('partials/head')
<html lang="en">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  
<body>
<header>	
	<nav class="navbar navbar-expand-lg navbar-light bg-dark justify-content-between dashboard_header">
  <a class="navbar-brand" href="#">Networked</a>
  
  <div class="right_nav">
  	<ul class="d-flex list-unstyled">
  		<li><a href="#"><i class="fa-regular fa-envelope"></i></a></li>
  		<li><a href="#"><i class="fa-regular fa-bell"></i></a></li>
        <li class="acc d-flex align-item-center"><img src="assets/img/acc.png" alt=""><span>John Doe</span><i class="fa-solid fa-chevron-down"></i></li>
  		<li class="darkmode"><a href="#"><i class="fa-solid fa-sun"></i></a></li>
  	</ul>
  </div>
</nav>
</header>
<main class="col bg-faded py-3 flex-grow-1">

@yield('content')

</main>
<footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>
  jQuery('.setting_btn').each(function () {
  jQuery(this).on('click', function () {
    jQuery(this).siblings('.setting_list').toggle();
  });
});

</script>
</footer>
</body>
</html>