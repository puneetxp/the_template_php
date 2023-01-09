<?php

function header_page($data)
{ ?>
    <header>
        <nav class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand vuehref text-center" href="/">Home</a>
                <button class="navbar-toggler bootstrap" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <?php if ($data == false) { ?>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link vuehref text-center" active-class="active" aria-current="page" href="/">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link vuehref text-center" active-class="active" href="/login">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link vuehref text-center" active-class="active" href="/register">Pricing</a>
                            </li>
                        </ul>
                    <?php } ?>
                    <?php if ($data == true) { ?>
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link vuehref text-center" active-class="active" href="/dashboard">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link vuehref text-center" active-class="active" href="/product">Product</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link vuehref text-center" active-class="active" href="/admin/product">Admin Product</a>
                            </li>
                            <li class="nav-item">
                                <button class="btn btn-secondary" @click="logoutService()">Logout</button>
                            </li>
                        </ul>
                    <?php } ?>
                </div>
            </div>
        </nav>
    </header>
<?php } ?>