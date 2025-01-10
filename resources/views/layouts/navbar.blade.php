<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light fixed-top" id="navbar">

  <h1 class="ml-3 font-weight-bold text-uppercase" style="font-size: 2rem;">{{ Auth::user()->nama_pt }}</h1>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Toggle Switch for Dark Mode -->
    <li class="nav-item d-flex align-items-center">
      <span id="darkModeIcon" class="mr-2"><i class="fas fa-sun"></i></span>
      <label class="switch">
        <input type="checkbox" id="darkModeToggle">
        <span class="slider round"></span>
      </label>
    </li>

    <!-- Navbar Search -->
    <li class="nav-item">
      <a class="nav-link" data-widget="navbar-search" href="#" role="button">
        <i class="fas fa-search"></i>
      </a>
      <div class="navbar-search-block">
        <form class="form-inline">
          <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
              </button>
              <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </li>

    <!-- Messages Dropdown Menu -->
    <!-- Notifications Dropdown Menu -->

    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>

    <!-- Logout Button -->
    <li class="nav-item align-items-center">
      <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm logout-button" style="margin-top: 5px; padding: 5px 10px;">
          <i class="fas fa-sign-out-alt"></i> Log Out
        </button>
      </form>
    </li>
  </ul>
</nav>

<!-- JavaScript for Dark Mode -->
<script>
  const darkModeToggle = document.getElementById('darkModeToggle');
  const navbar = document.getElementById('navbar');
  const body = document.body;
  const darkModeIcon = document.getElementById('darkModeIcon');

  darkModeToggle.addEventListener('change', function () {
    // Toggle dark mode classes
    navbar.classList.toggle('navbar-dark');
    navbar.classList.toggle('navbar-light');
    navbar.classList.toggle('bg-dark');
    navbar.classList.toggle('bg-white');

    // Toggle dark mode for body
    body.classList.toggle('dark-mode');

    // Change icon between sun and moon
    if (darkModeToggle.checked) {
      darkModeIcon.innerHTML = '<i class="fas fa-moon"></i>';
    } else {
      darkModeIcon.innerHTML = '<i class="fas fa-sun"></i>';
    }
  });
</script>

<!-- Add this CSS for Dark Mode Toggle Switch, Dark Mode Styling, and Icon Alignment -->
<style>
  /* Style for Toggle Switch */
  .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 25px;
    margin-left: 5px;
    margin-top: 5px; /* Tambahkan margin-top agar sejajar */
  }

  .switch input {
    display: none;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 25px;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 2.5px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
  }

  input:checked + .slider {
    background-color: #2196F3;
  }

  input:checked + .slider:before {
    transform: translateX(25px);
  }

  /* Dark Mode Styling */
  .dark-mode {
    background-color: #121212;
    color: #ffffff;
  }

  .dark-mode a {
    color: #ffffff;
  }

  .navbar-dark .navbar-nav .nav-link {
    color: #000000 !important; /* Ensuring icons remain white in dark mode */
  }

  .navbar-light .navbar-nav .nav-link {
    color: #000000 !important; /* Ensuring icons remain black in light mode */
  }

  /* Ensure the sidebar is visible in dark mode */
  .dark-mode .main-sidebar {
    background-color: #343a40;
  }

  /* Align the toggle switch with the rest of the navbar */
  .nav-item .switch {
    vertical-align: middle;
  }

  /* Icon styles for sun and moon */
  #darkModeIcon {
    font-size: 1.2rem;
    color: #00bbff;
  }

  .dark-mode #darkModeIcon {
    color: #f0e68c;
  }
  /* Styling .table-striped untuk dark mode */
.dark-mode .table-striped tbody tr:nth-of-type(odd) {
    background-color: #2e2e2e; /* Warna latar belakang baris ganjil */
}

.dark-mode .table-striped tbody tr:nth-of-type(even) {
    background-color: #3a3a3a; /* Warna latar belakang baris genap */
}

.dark-mode .table-striped tbody tr {
    color: #ffffff; /* Warna teks di mode gelap */
}
/* Warna teks label saat dark mode */
.dark-mode label {
    color: #ffffff;
}

/* Styling untuk h1-h5 di dark mode */
.dark-mode h1, .dark-mode h2, .dark-mode h3, .dark-mode h4, .dark-mode h5 {
    color: #ffffff; /* Warna teks heading untuk dark mode */
}
/* Dark mode styling for the chart container */
.dark-mode .card.bg-gradient-secondary {
    background-color: #1e1e1e; /* Warna background card untuk tabel */
    color: #ffffff;
}


  /* Ensuring all navbar icons are visible */
  .navbar .nav-link i {
    color: inherit !important; /* Ensure icons use the correct color based on the mode */
  }

  /* Styling for Logout Button */
  .logout-button {
    transition: background-color 0.3s ease, transform 0.3s ease;
  }

  .logout-button:hover {
    background-color: #ff4d4d; /* Change the background color on hover */
    transform: translateY(-2px); /* Move the button up slightly */
  }
</style>
