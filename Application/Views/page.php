<?php  use Core\Classes\View; ?>

<main class="page">
    <div class="jumbotron">
        <div class="container">
            <h1>Demo Page</h1><br>
            <p>This view has been loaded by <code>PageController</code> </p>

        </div>
    </div>
    <article class="article">
        <div class="container">
            <h3 class="title"><?= View::getData("page")->getTitle() ?></h3>
            <p class="content"><?= View::getData("page")->getContent() ?></p>
            <i class="date"><small>created on: <?= parse_date(View::getData("page")->getCreatedAt()) ?></small></i>
        </div>
    </article>
</main>
