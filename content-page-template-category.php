<?php
/**
 * Template part for displaying page content in page-template-category.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package bazaart
 */

?>
<style>
    .main-header-transparent {
        margin-top: 6em;
        padding: 72px 350px 0px 352px;
        text-align: center;
    }

    .template-group {
        list-style: none;
        display: grid;
        flex-wrap: wrap;
        gap: 16px;
        margin: 0;
        padding: 0px 0px;
        grid-template-columns: repeat(6, 1fr);
        justify-items: center;
    }

    .template-item {
        text-align: center;
        vertical-align: middle;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
        margin: 0;
        padding: 0;
    }

    ol.location-map-group {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        margin: 0;
        padding: 0;
		font-family:New Hero;
		gap: 4px;
    }

    ol.location-map-group li {

    }

    .location-map-item .location {
        padding: 4px 1em;
        border-radius: 100px;
        line-height: 1.5;
        color: #666666;
        /* margin: 0 0.68rem 0.68rem 0; */
		padding: 4px 8px 4px 8px;
        white-space: nowrap;
        display: inline-block;
        vertical-align: middle;
        text-overflow: ellipsis;
        max-width: 100%;
        overflow: hidden;
        text-decoration: none;
    }

    .location-map-group .arrow {
        font-size: 11px;
        color: #aeb3b9;
		padding:9px 0px 2px 0px;
        display: inline-block;
        vertical-align: middle;
    }

    .location-map-item .arrow {
        margin: 0 8px;
    }

    .location-map-item a {
        text-decoration: none;
    }
</style>
<style>
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
    section {
        padding: 72px 350px 0px 352px;
    }
</style>
<?php
$category_id = 132;
$category = [];
if ($category_id) {
    $ch = curl_init();

    // getting mailinglists first
    curl_setopt($ch, CURLOPT_URL, "https://api.bazaart.me/api/v6/admin_template_category/{$category_id}/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

    $headers = [];
    $headers[] = "Content-Type: application/json";
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $category = curl_exec($ch);
    if (curl_errno($ch)) {
        echo curl_error($ch);
    } else {
        $category = json_decode($category, true);
        curl_setopt($ch, CURLOPT_URL, "https://api.bazaart.me/api/v6/template_upload/?categories={$category_id}&limit=0&video=&version__lte=1000");
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo curl_error($ch);
        } else {
            $json_result = json_decode($result, true);
            $templates_list = $json_result["objects"];
            // $result = json_decode($result, true);
            // $category["templates"] = $result["objects"];
        }
    }
    curl_close($ch);
}

?>
<article id="post-">
  <div class="alignwide has-global-padding is-layout-constrained wp-block-group-is-layout-constrained"
       style="margin-top: clamp(32px, 5vw, 72px);">

    <div class="main-header-transparent">
      <div class="entry-content">
        <nav>
          <ol class="location-map-group">
            <li class="location-map-item">
				<a class="" href="/templates"><span class="location">Templates</span></a>
			</li>
				<small class="arrow">❯</small>
            <li class="location-map-item">
              <span class="location"><?php echo $category['name']; ?></span>
            </li>
          </ol>
        </nav>
      </div>
      <h1 class="entry-title entry-content is-style-width-narrow home-top-text"><?php echo $category['name']; ?></h1>
      <p class="is-style-width-normal has-medium-font-size entry-content"><?php echo $category['description']; ?></p>
    </div>
    <section>
      <div class="entry-content wrap-inner">
        <div class="category-templates-container">
          <ul class="template-group">
              <?php
              foreach ($templates_list as $template) {
                  ?>
                <li class="template-item">
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
                    <p class="template-name"><?php echo $template['name']; ?></p>
                  </a>
                </li>
              <?php } ?>
          </ul>
        </div>

      </div>
    </section>


  </div>
</article>