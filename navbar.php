<nav class="navbar navbar-expand-lg bg-secondary">
        <div class="container-fluid mx-5">
          <a class="navbar-brand text-white" href="index.php">Movie Archive</a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
              <li class="nav-item">
                <a class="nav-link active text-white" aria-current="page" href="index.php">Home</a>
              </li>
              <li class="nav-item mx-3">
                <a class="nav-link active text-white" aria-current="page" href="movielist.php">All movies</a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  Genre
                </a>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="genre.php?genre=Action">Action</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Adventure">Adventure</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Fantasy">Fantasy</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Thriller">Thriller</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Mystery">Mystery</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Crime">Crime</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Sci-fi">Sci-fi</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Drama">Drama</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Biography">Biography</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Comedy">Comedy</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Animation">Animation</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=War">War</a></li>
                  <li><a class="dropdown-item" href="genre.php?genre=Horror">Horror</a></li>
                </ul>
              </li>
            </ul>
            <!-- Begin Search -->
            <form class="d-flex" role="search" method="GET" action="searching.php">
              <input name="judul" class="form-control me-2" type="text" placeholder="Search" aria-label="Search">
              <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
            <!-- End search -->
        </div>
        </div>
    </nav>