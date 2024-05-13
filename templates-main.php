<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 0);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
$headers = [];
$headers[] = "Content-Type: application/json";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// get super categories
$urlGetSuperCategories = 'https://bazaart.me/api/v6/templateuploadcategorycollection/';

curl_setopt($ch, CURLOPT_URL, $urlGetSuperCategories);
$superCategoriesResult = curl_exec($ch);
$superCategories = [];
if (curl_errno($ch)) {
    echo curl_error($ch);
} else {
    $superCategories = json_decode($superCategoriesResult, true);
    $superCategories = $superCategories["objects"];
}

// get active super category
$url = $_SERVER['HTTP_HOST'];
$url.= $_SERVER['REQUEST_URI'];
$urlComponents = parse_url($url);
$activeCategoryId = null;
if(isset($urlComponents['query'])) {
    parse_str($urlComponents['query'],$params);
    $activeCategoryId = array_key_exists('id',$params) && !empty($params['id']) ? $params['id'] : $superCategories[0]['id'];
} else {
  if(isset($superCategories[0]['id'])){
      $activeCategoryId = $superCategories[0]['id'];
  }
}
// get sub categories
$urlGetSubCategories = "https://bazaart.me/api/v6/admin_template_category/?collections={$activeCategoryId}&locales=he&isHidden=false&contains_video=false&limit=100&version__lte=13";
curl_setopt($ch, CURLOPT_URL, $urlGetSubCategories);
$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo curl_error($ch);
} else {
    $json_result = json_decode($result, true);
    $categories_list = $json_result["objects"];
    foreach ($categories_list as &$category) {
        curl_setopt($ch, CURLOPT_URL, "https://api.bazaart.me/api/v6/template_upload/?categories={$category['id']}&limit=0&video=&version__lte=1000");
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo curl_error($ch);
        } else {
            $result = json_decode($result, true);
            $category["templates"] = $result["objects"];
        }
    }
}
curl_close($ch);
?>

<script src="
https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js
"></script>
<link href="
https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/css/splide.min.css
" rel="stylesheet">
<style>
    /* templates main list block */
    body .is-layout-constrained .wrap-templates-main {
        margin-top: clamp(48px, 4vw, 80px);
    }

    .splide-list-wraper {
        position: relative;
    }

    .splide__arrow--prev, .splide__arrow--next {
        font-size: 0;
        line-height: 0;
        position: absolute;
        top: 50%;
        display: block;
        width: 20px;
        height: 20px;
        padding: 0;
        -webkit-transform: translate(0, -50%);
        -ms-transform: translate(0, -50%);
        transform: translate(0, -50%);
        cursor: pointer;
        color: transparent;
        border: none;
        outline: none;
        background: transparent;
    }

    .splide__arrow.splide__arrow--prev, .splide__arrow.splide__arrow--next {
        width: 61px;
        height: 61px;
        z-index: 3;
    }

    .splide__arrow--prev:before, .splide__arrow--next:before {
        background: transparent url(data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGcgZmlsdGVyPSJ1cmwoI2ZpbHRlcjBfYl8xMjVfMTE0NTUpIj4KPGNpcmNsZSBjeD0iMjgiIGN5PSIyOCIgcj0iMjgiIHRyYW5zZm9ybT0ibWF0cml4KC0xIDAgMCAxIDU4IDIpIiBmaWxsPSIjRjJGMkYyIiBmaWxsLW9wYWNpdHk9IjAuNSIgc3R5bGU9ImZpbGw6I0YyRjJGMjtmaWxsOmNvbG9yKGRpc3BsYXktcDMgMC45NDkwIDAuOTQ5MCAwLjk0OTApO2ZpbGwtb3BhY2l0eTowLjU7Ii8+CjxjaXJjbGUgY3g9IjI4IiBjeT0iMjgiIHI9IjI3LjUiIHRyYW5zZm9ybT0ibWF0cml4KC0xIDAgMCAxIDU4IDIpIiBzdHJva2U9IiNFNUU1RTUiIHN0eWxlPSJzdHJva2U6I0U1RTVFNTtzdHJva2U6Y29sb3IoZGlzcGxheS1wMyAwLjg5ODAgMC44OTgwIDAuODk4MCk7c3Ryb2tlLW9wYWNpdHk6MTsiLz4KPC9nPgo8cGF0aCBkPSJNMzQgMjkuMDg5OUMzNCAyOS4yNzggMzMuOTI0IDI5LjQzODIgMzMuNzg2NiAyOS41Nzk2TDI3LjE0NTEgMzUuOTgzN0MyNy4wMjE3IDM2LjExNDQgMjYuODU0MiAzNi4xNzk4IDI2LjY2NTMgMzYuMTc5OEMyNi4yOTAyIDM2LjE3OTggMjYgMzUuODk2MiAyNiAzNS41MTQ1QzI2IDM1LjMyOSAyNi4wNzI3IDM1LjE2ODggMjYuMTkyOSAzNS4wNDEzTDMyLjI4NTggMjkuMTYxOUMzMi4zMjY1IDI5LjEyMjYgMzIuMzI2NSAyOS4wNTczIDMyLjI4NTggMjkuMDE4TDI2LjE5MjkgMjMuMTM4NUMyNi4wNzI3IDIzLjAxMTEgMjYgMjIuODQzNSAyNiAyMi42NjUzQzI2IDIyLjI4MzYgMjYuMjkwMiAyMiAyNi42NjUzIDIyQzI2Ljg1NDIgMjIgMjcuMDIxNyAyMi4wNjU0IDI3LjE0NTEgMjIuMTk2MkwzMy43ODY2IDI4LjYwMDJDMzMuOTI0IDI4Ljc0MTYgMzQgMjguOTAxOCAzNCAyOS4wODk5WiIgZmlsbD0iIzRENEQ0RCIgc3R5bGU9ImZpbGw6IzRENEQ0RDtmaWxsOmNvbG9yKGRpc3BsYXktcDMgMC4zMDIwIDAuMzAyMCAwLjMwMjApO2ZpbGwtb3BhY2l0eToxOyIvPgo8ZGVmcz4KPGZpbHRlciBpZD0iZmlsdGVyMF9iXzEyNV8xMTQ1NSIgeD0iLTIiIHk9Ii0yIiB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIGZpbHRlclVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgY29sb3ItaW50ZXJwb2xhdGlvbi1maWx0ZXJzPSJzUkdCIj4KPGZlRmxvb2QgZmxvb2Qtb3BhY2l0eT0iMCIgcmVzdWx0PSJCYWNrZ3JvdW5kSW1hZ2VGaXgiLz4KPGZlR2F1c3NpYW5CbHVyIGluPSJCYWNrZ3JvdW5kSW1hZ2VGaXgiIHN0ZERldmlhdGlvbj0iMiIvPgo8ZmVDb21wb3NpdGUgaW4yPSJTb3VyY2VBbHBoYSIgb3BlcmF0b3I9ImluIiByZXN1bHQ9ImVmZmVjdDFfYmFja2dyb3VuZEJsdXJfMTI1XzExNDU1Ii8+CjxmZUJsZW5kIG1vZGU9Im5vcm1hbCIgaW49IlNvdXJjZUdyYXBoaWMiIGluMj0iZWZmZWN0MV9iYWNrZ3JvdW5kQmx1cl8xMjVfMTE0NTUiIHJlc3VsdD0ic2hhcGUiLz4KPC9maWx0ZXI+CjwvZGVmcz4KPC9zdmc+Cg==) no-repeat 50% 50%;
        background-size: 61px 61px;
        content: "";
        width: 61px;
        height: 61px;
        display: block;
        opacity: 1;
        transition: filter 600ms ease;
        filter: drop-shadow(-10px 10px 20px rgba(13, 7, 58, 0.4));
        transform: rotateX(-180deg);
    }

    .splide__arrow--prev {
        margin-left: -30px;
    }

    .splide__arrow--prev:before {
        transform: scaleX(-1);
    }

    .splide__arrow--next {
        margin-right: -30px;
    }

    .splide__arrow:disabled {
        display: none;
    }

    .splide__pagination {
        /* hack because component prop & display none working bad on safari*/
        opacity: 0 !important;
    }

    .splide__list.h-auto {
        height: auto;
    }
    body {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        font-smooth: always;
        overflow: hidden;
        overflow-x: hidden;
        overflow-y: scroll;
        overscroll-behavior: contain;
        -webkit-overflow-scrolling: touch;
    }

    img {
        max-width: 100%;
        height: auto;
        border: none;
        border-radius: 8px;
    }

    main {
        width: 1248px;
        height: 1501px;
        position: absolute;
        top: 468px;
        left: 352px;
        gap: 40px;
    }

    .category-header {
        width: 100%;
        height: 28px;
		margin-bottom: 16px;
		display: flex;
    	justify-content: space-between;

    div {
        font-family: New Hero;
        font-size: 20px;
        font-weight: 700;
        line-height: 28px;
        text-align: left;
    }

    a {
        height: 24px;
        color: #4D4D4D;
        text-decoration: none;
		line-height: 28px;
    }

    }
    .template-item {

    a {
        text-decoration: none;
        color: black;
    }

    .template-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
        color: #666666;
		font-size:12px;
		margin-top: 10px;
    }

    }

    .super-categories-container {
        width: 100%;
    }

    .super-categories-content {
        overflow: hidden;
        background-color: #F2F2F2;
        display: flex;
		gap: 10px;
        justify-content: center;
        font-size: 14px;
        font-weight: 500;
        border-radius: 50px;
        padding: 6px;
        width: fit-content;
        margin: 0 auto;
    }

    .super-categories-content .super-categories-item {
        float: left;
        color: #666666;
        text-align: center;
        padding: 8px 20px;
        text-decoration: none;
        border-radius: 50px;
    }

    .super-categories-content a:hover {
        background-color: #ddd;
        color: black;
    }

    .super-categories-content a.active {
        background-color: #FFFFFF;
        color: #000000;
    }
</style>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var elms = document.getElementsByClassName('splide')
    for (var i = 0; i < elms.length; i++) {
      new Splide(elms[i], {
        perPage: 6,
        gap: '1rem',
        lazyLoad: 'nearby',
        breakpoints: {
          782: {
            perPage: 2,
            arrows: false,
            padding: { right: '20%', left: '2.125em' },
          },
          1024: {
            perPage: 3,
            arrows: false,
            padding: { right: '20%', left: '4em' },
          },
        },
      }).mount()
    }
  })
</script>
<main id="primary" class="">
  <div class="super-categories-container">
    <div class="super-categories-content">
      <?php
        foreach ($superCategories as $category) {
          $active = $activeCategoryId == $category['id'] ? 'active' :  '';
      ?>
      <a class="super-categories-item <?php echo $active;?>" href="templates-main.php?id=<?php echo $category['id'];?>"><?php echo $category['name'];?></a>
        <?php } ?>
    </div>
  </div>
  <section>
    <div class="entry-content">
      <!--          <p>--><?php //$category_id = get_query_var('category_id');
        //                    echo $category_id;?><!--</p>-->

        <?php
        foreach ($categories_list as $category) {
            ?>

          <div class="category-container">

            <section class="splide" aria-labelledby="carousel-heading">
              <div class="category-header">
                <div><?php echo $category['name']; ?></div>
                <a href="category_view/<?php echo $category['id']; ?>">See all</a>
              </div>
              <div class="splide-list-wraper">
                <div class="splide__arrows">
                  <button class="splide__arrow splide__arrow--prev"></button>
                  <button class="splide__arrow splide__arrow--next"></button>
                </div>
                <div class="splide__track">
                  <ul class="splide__list h-auto">
                      <?php
                      foreach ($category['templates'] as $template) {
                          ?>
                        <li class="template-item splide__slide">
                          <a href="https://design.bazaart.me/design/<?php echo $template['id']; ?>/edit">
                              <?php
                              $extension = pathinfo($template['image'], PATHINFO_EXTENSION);
                              ?>
                            <img class="<?php echo in_array($extension, [
                                'webp',
                                'png',
                                'heic',
                            ]) ? 'opacity-bg:rgular-bg' : '' ?>"
                                 src="<?php echo $template['image']; ?>"
                                 alt="<?php echo $template['name']; ?>">
                            <div class="template-name"><?php echo $template['name']; ?></div>
                          </a>
                        </li>
                      <?php } ?>
                  </ul>
                </div>
              </div>
            </section>
          </div>
        <?php break;} ?>
    </div>
  </section>
</main><!-- #main -->