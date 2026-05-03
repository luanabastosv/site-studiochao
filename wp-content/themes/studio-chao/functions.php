<?php
// Enqueue parent theme styles e scripts
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        ['parent-style'],
        '1.0.0'
    );
    wp_enqueue_script(
        'hero-slider',
        get_stylesheet_directory_uri() . '/assets/js/hero-slider.js',
        [],
        '1.0.0',
        true
    );

    // GSAP + ScrollTrigger — no <head> para animar o loader imediatamente
    wp_enqueue_script('gsap', get_stylesheet_directory_uri() . '/assets/js/gsap.min.js', [], '3.12.5', false);
    wp_enqueue_script('gsap-scrolltrigger', get_stylesheet_directory_uri() . '/assets/js/ScrollTrigger.min.js', ['gsap'], '3.12.5', false);
    wp_enqueue_script('gsap-animations', get_stylesheet_directory_uri() . '/assets/js/animations.js', ['gsap', 'gsap-scrolltrigger'], '1.0.0', false);
});

// Registrar CPT: Projetos
add_action('init', function() {
    register_post_type('projetos', [
        'labels' => [
            'name'               => 'Projetos',
            'singular_name'      => 'Projeto',
            'add_new'            => 'Adicionar novo',
            'add_new_item'       => 'Adicionar novo projeto',
            'edit_item'          => 'Editar projeto',
            'new_item'           => 'Novo projeto',
            'view_item'          => 'Ver projeto',
            'search_items'       => 'Buscar projetos',
            'not_found'          => 'Nenhum projeto encontrado',
            'not_found_in_trash' => 'Nenhum projeto na lixeira',
        ],
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => ['slug' => 'projetos'],
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt'],
        'menu_icon'     => 'dashicons-building',
        'show_in_rest'  => true,
    ]);
});

add_action('wp_dashboard_setup', function() {
    wp_add_dashboard_widget(
        'tutorial_video',
        'Tutorial — Studio Chão',
        function() {
            ?>
            <iframe width="100%" height="315" src="https://www.youtube.com/embed/nyX1t9oR11g" frameborder="0" allowfullscreen></iframe>
            <?php
        }
    );
});

// Registrar CPT: Equipe
add_action('init', function() {
    register_post_type('equipe', [
        'labels' => [
            'name'               => 'Equipe',
            'singular_name'      => 'Membro',
            'add_new'            => 'Adicionar novo',
            'add_new_item'       => 'Adicionar novo membro',
            'edit_item'          => 'Editar membro',
            'new_item'           => 'Novo membro',
            'view_item'          => 'Ver membro',
            'search_items'       => 'Buscar membros',
            'not_found'          => 'Nenhum membro encontrado',
            'not_found_in_trash' => 'Nenhum membro na lixeira',
        ],
        'public'        => true,
        'has_archive'   => false,
        'rewrite'       => ['slug' => 'equipe'],
        'supports'      => ['title', 'thumbnail'],
        'menu_icon'     => 'dashicons-groups',
        'show_in_rest'  => true,
    ]);
});

// Registrar Taxonomy: Cargo Equipe
add_action('init', function() {
    register_taxonomy('cargo', 'equipe', [
        'labels' => [
            'name'          => 'Cargos',
            'singular_name' => 'Cargo',
            'menu_name'     => 'Cargos',
        ],
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['slug' => 'cargo'],
        'show_in_rest' => true,
    ]);
});

// Registrar Taxonomy: Categoria de Projeto
add_action('init', function() {
    register_taxonomy('categoria-projeto', 'projetos', [
        'labels' => [
            'name'              => 'Categorias',
            'singular_name'     => 'Categoria',
            'search_items'      => 'Buscar categorias',
            'all_items'         => 'Todas as categorias',
            'edit_item'         => 'Editar categoria',
            'update_item'       => 'Atualizar categoria',
            'add_new_item'      => 'Adicionar nova categoria',
            'new_item_name'     => 'Nome da nova categoria',
            'menu_name'         => 'Categorias',
        ],
        'hierarchical'  => true,
        'public'        => true,
        'rewrite'       => ['slug' => 'categoria-projeto'],
        'show_in_rest'  => true,
    ]);
});

// Customizer: Hero Slider
add_action('customize_register', function($wp_customize) {
    $wp_customize->add_section('hero_slider', [
        'title'    => 'Hero - Slider',
        'priority' => 30,
    ]);

    for ($i = 1; $i <= 4; $i++) {
        $wp_customize->add_setting("hero_slide_{$i}", [
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ]);
        $wp_customize->add_control(new WP_Customize_Image_Control(
            $wp_customize,
            "hero_slide_{$i}",
            [
                'label'   => "Imagem {$i} do hero",
                'section' => 'hero_slider',
            ]
        ));
    }
});

// Shortcode Hero Slider
add_shortcode('hero_slider', function() {
    $output = '<div class="hero-slider">';
    for ($i = 1; $i <= 4; $i++) {
        $img = get_theme_mod("hero_slide_{$i}");
        if ($img) {
            $output .= '<div class="hero-slide"><img src="' . esc_url($img) . '" alt="Hero ' . $i . '"></div>';
        }
    }
    $output .= '</div>';
    return $output;
});

add_filter('upload_mimes', function($mimes) {
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
});

// Shortcode Projeto em Destaque
add_shortcode('projeto_destaque', function($atts) {
    $atts = shortcode_atts([
        'categoria' => 'projeto-destaque',
    ], $atts);

    $args = [
        'post_type'      => 'projetos',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if (!empty($atts['categoria'])) {
        $args['tax_query'] = [[
            'taxonomy' => 'categoria-projeto',
            'field'    => 'slug',
            'terms'    => $atts['categoria'],
        ]];
    }

    $projeto = get_posts($args);

    if (empty($projeto)) return '';

    $p        = $projeto[0];
    $id       = $p->ID;
    $titulo   = get_the_title($id);
    $excerpt  = get_the_excerpt($id);
    $link     = get_permalink($id);
    $cats     = get_the_terms($id, 'categoria-projeto');
    $cat_name = '';
    if ($cats) {
        foreach ($cats as $cat) {
            if ($cat->slug !== 'projeto-destaque') {
                $cat_name = $cat->name;
                break;
            }
        }
    }

    $thumb      = get_the_post_thumbnail_url($id, 'full');
    $second_img = '';

    $galeria = get_field('galeria', $id);
    if ($galeria && count($galeria) >= 2) {
        $thumb      = $galeria[0]['url'];
        $second_img = $galeria[1]['url'];
    } elseif ($galeria && count($galeria) === 1) {
        $thumb = $galeria[0]['url'];
    }
    if (!$thumb) $thumb = get_the_post_thumbnail_url($id, 'full');

    ob_start(); ?>
    <div class="destaque-inner">
        <div class="destaque-col-principal">
            <figure class="destaque-img-principal">
                <a href="<?php echo esc_url($link); ?>">
                    <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($titulo); ?>"/></a>
            </figure>
            <div class="destaque-legenda">
                <p class="destaque-legenda-titulo"><strong><?php echo esc_html($titulo); ?></strong> | <?php echo esc_html(strtolower($cat_name)); ?></p>
                <p class="destaque-legenda-link"><a href="<?php echo esc_url($link); ?>">ver mais</a></p>
            </div>
        </div>
        <div class="destaque-col-secundaria">
            <?php if ($excerpt): ?>
            <p class="destaque-excerpt"><?php echo esc_html($excerpt); ?></p>
            <?php endif; ?>
            <?php if ($second_img): ?>
            <figure class="destaque-img-secundaria">
                <img src="<?php echo esc_url($second_img); ?>" alt="<?php echo esc_attr($titulo); ?> 2"/>
            </figure>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});

// Shortcode Hero Projetos
add_shortcode('hero_projetos', function() {
    $projeto = get_posts([
        'post_type'      => 'projetos',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => [[
            'taxonomy' => 'categoria-projeto',
            'field'    => 'slug',
            'terms'    => 'projeto-destaque',
        ]],
    ]);

    if (empty($projeto)) return '';

    $id    = $projeto[0]->ID;
    $thumb = get_the_post_thumbnail_url($id, 'full');

    if (!$thumb) return '';

    ob_start(); ?>
    <div class="projetos-hero-bg" style="background-image: url('<?php echo esc_url($thumb); ?>')"></div>
    <?php
    return ob_get_clean();
});

// ========================
// SHORTCODES — FRONT PAGE
// ========================

add_shortcode('fp_intro_logo', function() {
    $id  = get_option('page_on_front');
    $img = get_field('fp_intro_logo', $id);
    if (!$img) return '';
    return '<figure class="wp-block-image size-full intro-logo"><img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt']) . '"/></figure>';
});

add_shortcode('fp_catalogo', function() {
    $id = get_option('page_on_front');

    $items = [
        'corporativo' => get_field('fp_catalogo_corporativo', $id),
        'residencial' => get_field('fp_catalogo_residencial', $id),
        'comercial'   => get_field('fp_catalogo_comercial', $id),
    ];

    $out = '<div class="wp-block-columns catalogo-grid">';
    foreach ($items as $slug => $img) {
        $url  = $img ? esc_url($img['url']) : '';
        $link = home_url('/categoria-projeto/' . $slug);
        $out .= '<div class="wp-block-column">';
        $out .= '<div class="wp-block-group catalogo-item">';
        $out .= '<figure class="wp-block-image size-full">';
        $out .= '<a href="' . esc_url($link) . '" aria-label="Ver projetos ' . esc_attr($slug) . '">';
        $out .= $url ? '<img src="' . $url . '" alt="' . esc_attr($slug) . '" style="aspect-ratio:3/4;object-fit:cover"/>' : '';
        $out .= '</a></figure>';
        $out .= '<p class="catalogo-label">' . esc_html($slug) . '</p>';
        $out .= '</div></div>';
    }
    $out .= '</div>';
    return $out;
});

add_shortcode('fp_quemsomos_foto', function() {
    $id  = get_option('page_on_front');
    $img = get_field('fp_quemsomos_foto', $id);
    if (!$img) return '';
    return '<figure class="wp-block-image size-full quemsomos-foto"><img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt']) . '" style="aspect-ratio:1;object-fit:cover"/></figure>';
});

add_shortcode('fp_quemsomos_texto', function() {
    $id   = get_option('page_on_front');
    $text = get_field('fp_quemsomos_texto', $id);
    if (!$text) return '';
    return '<p>' . esc_html($text) . '</p>';
});

// Shortcode Meta do Projeto
add_shortcode('single_meta', function() {
    $localizacao = get_field('localizacao');
    $area        = get_field('area');
    $data_conclusao = get_field('data_conclusao');
    $fotografo   = get_field('fotografo');
    $servico     = get_field('servico');
    $cats        = get_the_terms(get_the_ID(), 'categoria-projeto');
    $cat_name    = '';
    if ($cats) {
        foreach ($cats as $cat) {
            if ($cat->slug !== 'projeto-destaque') {
                $cat_name = $cat->name;
                break;
            }
        }
    }

    ob_start(); ?>
<div class="single-meta-items">
    <?php if ($cat_name || $localizacao): ?>
    <p class="single-meta-item single-meta-item-top">
        <?php if ($cat_name): ?>
        <span class="single-meta-label">Projeto</span><strong><?php echo esc_html($cat_name); ?></strong>
        <?php endif; ?>
        <?php if ($localizacao): ?>
        <span class="single-meta-sep">—</span><span class="single-meta-loc"><?php echo esc_html($localizacao); ?></span>
        <?php endif; ?>
    </p>
    <?php endif; ?>
    <?php if ($servico): ?>
    <p class="single-meta-item">
        <span class="single-meta-label">Serviço</span><?php echo esc_html($servico); ?>
    </p>
    <?php endif; ?>
    <?php if ($area): ?>
    <p class="single-meta-item">
        <span class="single-meta-label">Área</span><?php echo esc_html($area); ?>
    </p>
    <?php endif; ?>
    <?php if ($data_conclusao): ?>
<p class="single-meta-item">
    <span class="single-meta-label">Conclusão</span><?php echo esc_html($data_conclusao); ?>
</p>
<?php endif; ?>
    <?php if ($fotografo): ?>
    <p class="single-meta-item">
        <span class="single-meta-label">Fotos</span><?php echo esc_html($fotografo); ?>
    </p>
    <?php endif; ?>
</div>
<?php
    return ob_get_clean();
});

// Shortcode Galeria do Projeto
add_shortcode('galeria_projeto', function() {
    $imagens = get_field('galeria');
    if (!$imagens) return '';

    $total = count($imagens);
    $i = 0;

    ob_start();
    echo '<div class="galeria-feed">';

    while ($i < $total) {
        if (isset($imagens[$i])) {
            echo '<div class="galeria-linha galeria-full">';
            echo '<img src="' . esc_url($imagens[$i]['url']) . '" alt="' . esc_attr($imagens[$i]['alt']) . '">';
            echo '</div>';
            $i++;
        }

        if (isset($imagens[$i]) && isset($imagens[$i+1])) {
            echo '<div class="galeria-linha galeria-1-2">';
            echo '<img src="' . esc_url($imagens[$i]['url']) . '" alt="' . esc_attr($imagens[$i]['alt']) . '">';
            echo '<img src="' . esc_url($imagens[$i+1]['url']) . '" alt="' . esc_attr($imagens[$i+1]['alt']) . '">';
            echo '</div>';
            $i += 2;
        } elseif (isset($imagens[$i])) {
            echo '<div class="galeria-linha galeria-full">';
            echo '<img src="' . esc_url($imagens[$i]['url']) . '" alt="' . esc_attr($imagens[$i]['alt']) . '">';
            echo '</div>';
            $i++;
        }

        if (isset($imagens[$i]) && isset($imagens[$i+1])) {
            echo '<div class="galeria-linha galeria-2-1">';
            echo '<img src="' . esc_url($imagens[$i]['url']) . '" alt="' . esc_attr($imagens[$i]['alt']) . '">';
            echo '<img src="' . esc_url($imagens[$i+1]['url']) . '" alt="' . esc_attr($imagens[$i+1]['alt']) . '">';
            echo '</div>';
            $i += 2;
        } elseif (isset($imagens[$i])) {
            echo '<div class="galeria-linha galeria-full">';
            echo '<img src="' . esc_url($imagens[$i]['url']) . '" alt="' . esc_attr($imagens[$i]['alt']) . '">';
            echo '</div>';
            $i++;
        }
    }

    echo '</div>';
    return ob_get_clean();
});

// Shortcode Filtro de Projetos
add_shortcode('filtro_projetos', function() {
    $current_term = get_queried_object();
    $current_slug = '';
    if ($current_term instanceof WP_Term) {
        $current_slug = $current_term->slug;
    }

    $categorias = [
        ''            => 'Todos',
        'residencial' => 'Residencial',
        'corporativo' => 'Corporativo',
        'comercial'   => 'Comercial',
    ];

    $output = '<div class="projetos-filtro-nav">';
    foreach ($categorias as $slug => $label) {
        $url    = $slug ? home_url('/categoria-projeto/' . $slug . '/') : home_url('/projetos/');
        $active = ($slug === $current_slug || ($slug === '' && !$current_slug && !is_tax())) ? ' active' : '';
        $output .= '<a href="' . esc_url($url) . '" class="filtro-link' . $active . '">' . esc_html($label) . '</a>';
    }
    $output .= '</div>';

    $current_label = 'Todos';
    foreach ($categorias as $slug => $label) {
        if ($slug === $current_slug) {
            $current_label = $label;
            break;
        }
    }

    $output .= '<div class="projetos-filtro-dropdown" id="filtroDropdown">';
    $output .= '<div class="filtro-dropdown-trigger">';
    $output .= '<span class="projetos-filtro-dropdown-label">' . esc_html($current_label) . '</span>';
    $output .= '<span class="filtro-dropdown-arrow">↓</span>';
    $output .= '</div>';
    $output .= '<ul class="filtro-dropdown-menu">';
    foreach ($categorias as $slug => $label) {
        $url    = $slug ? home_url('/categoria-projeto/' . $slug . '/') : home_url('/projetos/');
        $active = ($slug === $current_slug || ($slug === '' && !$current_slug && !is_tax())) ? ' active' : '';
        $output .= '<li><a href="' . esc_url($url) . '" class="filtro-dropdown-item' . $active . '">' . esc_html($label) . '</a></li>';
    }
    $output .= '</ul>';
    $output .= '</div>';

    return $output;
});

// ========================
// SHORTCODES — QUEM SOMOS
// ========================

add_shortcode('qs_equipe_sc', function() {
    $membros = get_posts([
        'post_type'      => 'equipe',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'tax_query'      => [[
            'taxonomy' => 'cargo',
            'field'    => 'slug',
            'terms'    => 'socia',
            'operator' => 'NOT IN',
        ]],
    ]);

    if (empty($membros)) return '';

    $out = '<div class="equipe-grid">';
    foreach ($membros as $membro) {
        $id     = $membro->ID;
        $nome   = get_the_title($id);
        $funcao = get_field('funcao', $id);
        $capa   = get_field('foto_capa', $id);
        $extras = get_field('fotos_extras', $id);

        $capa_url = $capa ? esc_url($capa['url']) : '';

        $out .= '<div class="equipe-card"';
        if ($extras && count($extras) > 0) {
            $urls = array_map(fn($f) => esc_url($f['url']), $extras);
            $out .= ' data-fotos=\'' . json_encode($urls) . '\'';
        }
        $out .= '>';

        $out .= '<div class="equipe-card-capa">';
        if ($capa_url) {
            $out .= '<img src="' . $capa_url . '" alt="' . esc_attr($nome) . '" class="equipe-foto-capa">';
        }
        $out .= '<div class="equipe-card-info">';
        $out .= '<span class="equipe-nome">' . esc_html($nome) . '</span>';
        if ($funcao) $out .= '<span class="equipe-funcao">' . esc_html($funcao) . '</span>';
        $out .= '</div>';
        $out .= '</div>';

        $out .= '<div class="equipe-card-hover">';
        if ($extras) {
            foreach ($extras as $foto) {
                $out .= '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($nome) . '" class="equipe-foto-extra">';
            }
        }
        $out .= '</div>';

        $out .= '</div>';
    }
    $out .= '</div>';
    return $out;
});

add_shortcode('qs_hero', function() {
    $page = get_page_by_title('Quem Somos');
    if (!$page) return '';
    $img = get_field('qs_hero_img', $page->ID);
    if (!$img) return '';
    return '<img src="' . esc_url($img['url']) . '" alt="' . esc_attr($img['alt']) . '" class="qs-hero-img">';
});

add_filter('the_content', function($content) {
    $content = preg_replace('/<p>\s*(<img[^>]+class="qs-hero-img"[^>]*>)\s*<\/p>/', '$1', $content);
    return $content;
});

add_shortcode('qs_intro_texto', function() {
    $id    = get_queried_object_id();
    $texto = get_field('qs_intro_texto', $id);
    if (!$texto) return '';
    return '<p class="qs-intro-texto">' . esc_html($texto) . '</p>';
});

add_shortcode('qs_bio', function() {
    $id    = get_queried_object_id();
    $bio_1 = get_field('qs_bio_paragrafo_1', $id);
    $bio_2 = get_field('qs_bio_paragrafo_2', $id);
    $out   = '';
    if ($bio_1) $out .= '<p>' . esc_html($bio_1) . '</p>';
    if ($bio_2) $out .= '<p>' . esc_html($bio_2) . '</p>';
    return $out;
});

add_shortcode('qs_socias', function() {
    $socias = get_posts([
        'post_type'      => 'equipe',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'tax_query'      => [[
            'taxonomy' => 'cargo',
            'field'    => 'slug',
            'terms'    => 'socia',
        ]],
    ]);

    if (empty($socias)) return '';

    $out = '<div class="qs-fotos-grid">';
    foreach ($socias as $socia) {
        $id       = $socia->ID;
        $nome     = get_the_title($id);
        $funcao   = get_field('funcao', $id);
        $capa     = get_field('foto_capa', $id);
        $extras   = get_field('fotos_extras', $id);
        $capa_url = $capa ? esc_url($capa['url']) : '';

        $out .= '<div class="qs-socia-wrap">';

        $out .= '<div class="qs-foto-wrap equipe-card"';
        if ($extras && count($extras) > 0) {
            $urls = array_map(fn($f) => esc_url($f['url']), $extras);
            $out .= ' data-fotos=\'' . json_encode($urls) . '\'';
        }
        $out .= '>';

        $out .= '<div class="equipe-card-capa">';
        if ($capa_url) {
            $out .= '<img src="' . $capa_url . '" alt="' . esc_attr($nome) . '" class="qs-foto equipe-foto-capa">';
        }
        $out .= '</div>';

        $out .= '<div class="equipe-card-hover">';
        if ($extras) {
            foreach ($extras as $foto) {
                $out .= '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($nome) . '" class="equipe-foto-extra">';
            }
        }
        $out .= '</div>';

        $out .= '</div>';

        $out .= '<div class="qs-socia-info">';
        $out .= '<span class="qs-socia-nome">' . esc_html($nome) . '</span>';
        if ($funcao) $out .= '<span class="qs-socia-funcao">' . esc_html($funcao) . '</span>';
        $out .= '</div>';

        $out .= '</div>';
    }
    $out .= '</div>';
    return $out;
});

// ========================
// SCHEMA MARKUP
// ========================

add_action('wp_head', function() {
    $schema = [
        '@context'        => 'https://schema.org',
        '@type'           => ['LocalBusiness', 'ProfessionalService'],
        'name'            => 'Studio Chão',
        'alternateName'   => 'Studio Chão Arquitetura e Interiores',
        'description'     => 'Escritório de arquitetura e interiores em Fortaleza, CE. Projetos residenciais, corporativos e comerciais com propósito, sensibilidade e identidade. Fundado por Neuza Osório e Luana Bezerra.',
        'url'             => 'https://studiochao.com.br',
        'logo'            => get_stylesheet_directory_uri() . '/assets/images/logo-loader.svg',
        'telephone'       => '+55-85-98933-7852',
        'email'           => 'contato@studiochao.com.br',
        'address'         => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => 'Av. Antônio Sales, 3169 - sala 102 e 103',
            'addressLocality' => 'Fortaleza',
            'addressRegion'   => 'CE',
            'postalCode'      => '60135-203',
            'addressCountry'  => 'BR',
        ],
        'geo' => [
            '@type'     => 'GeoCoordinates',
            'latitude'  => '-3.7327',
            'longitude' => '-38.5016',
        ],
        'areaServed'      => 'Fortaleza, Ceará, Brasil',
        'serviceType'     => ['Arquitetura Residencial', 'Arquitetura Corporativa', 'Arquitetura Comercial', 'Design de Interiores'],
        'founder'         => [
            [
                '@type'    => 'Person',
                'name'     => 'Neuza Osório',
                'jobTitle' => 'Arquiteta e Sócia',
            ],
            [
                '@type'    => 'Person',
                'name'     => 'Luana Bezerra',
                'jobTitle' => 'Arquiteta e Sócia',
            ],
        ],
        'sameAs' => [
            'https://www.instagram.com/studiochao',
            'https://www.facebook.com/studiochao',
        ],
        'openingHoursSpecification' => [
            [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens'     => '09:00',
                'closes'    => '18:00',
            ],
        ],
    ];

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
});

add_action('wp_footer', function() {
?>

<div id="page-loader">
    <div id="loader-logo">
        <svg id="logo-loader-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 288.05 206.35">
            <path id="logo-a" d="M99.29,87.84v-10.79h33.58v126.62h-33.58v-9.83c-6.06,6.36-13.77,10.33-22.49,11.68-58.09,8.97-97.98-56.59-63.46-104.22,17.82-24.6,55.51-36.73,81.38-17.15,1.56,1.18,2.89,2.68,4.58,3.69ZM63.27,107.09c-13.09,1.16-24.66,12.1-27.99,24.53-8.02,29.86,26.13,54.91,51.18,35.1,25.79-20.39,9.58-62.54-23.18-59.63Z"/>
            <path id="logo-o" d="M216.53.13c59.4-3.57,93.74,64.95,54.62,109.77-33.3,38.14-95.49,25.99-112.37-21.46C144.07,47.13,172.71,2.77,216.53.13ZM217.73,31.79c-17.95,1.52-31.25,18.88-30.17,36.5,1.69,27.59,34.14,42.81,55.51,24.34,25.28-21.85,8.5-63.69-25.34-60.83Z"/>
            <polygon id="logo-barra-topo" points="132.87 34.36 0 34.36 0 .9 .36 .54 132.51 .54 132.87 .9 132.87 34.36"/>
            <polygon id="logo-barra-baixo" points="288.05 202.95 155.18 202.95 155.18 169.49 155.54 169.13 287.69 169.13 288.05 169.49 288.05 202.95"/>
        </svg>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" aria-hidden="true">
    <div id="lightbox-overlay"></div>
    <div id="lightbox-content">
        <img id="lightbox-img" src="" alt="">
        <button id="lightbox-prev" aria-label="Anterior">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <button id="lightbox-next" aria-label="Próximo">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>
        <button id="lightbox-close" aria-label="Fechar">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18"/><path d="M6 6l12 12"/></svg>
        </button>
    </div>
</div>

<!-- Botão hamburguer customizado -->
<button class="menu-toggle-custom" id="menuToggle" aria-label="Menu">
    <span class="menu-toggle-bar bar1"></span>
    <span class="menu-toggle-bar bar2"></span>
    <span class="menu-toggle-bar bar3"></span>
</button>

<!-- Menu mobile overlay -->
<div class="menu-mobile-overlay" id="menuMobile">
    <div class="menu-mobile-inner">
        <div class="menu-mobile-top">
            <a href="<?php echo home_url('/'); ?>" class="menu-mobile-logo">
                <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-loader.svg" alt="Studio Chão">
            </a>
        </div>
        <nav class="menu-mobile-nav">
            <a href="<?php echo home_url('/'); ?>" class="menu-mobile-link">Início</a>
            <a href="<?php echo home_url('/projetos'); ?>" class="menu-mobile-link">Projetos</a>
            <a href="<?php echo home_url('/quem-somos'); ?>" class="menu-mobile-link">Quem somos</a>
            <a href="https://api.whatsapp.com/send/?phone=5585981417148&text=Oi%21+Quero+fazer+um+projeto.&type=phone_number&app_absent=0" target="_blank" class="menu-mobile-cta">Fale conosco</a>
        </nav>
    </div>
</div>

<!-- Scroll Indicator -->
<div id="scroll-indicator">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 5v14"/>
        <path d="M18 13l-6 6-6-6"/>
    </svg>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

// ── LIGHTBOX ──
const lightbox = document.getElementById('lightbox');
if (lightbox) {
    const lightboxImg  = document.getElementById('lightbox-img');
    const prevBtn      = document.getElementById('lightbox-prev');
    const nextBtn      = document.getElementById('lightbox-next');
    const closeBtn     = document.getElementById('lightbox-close');
    const overlay      = document.getElementById('lightbox-overlay');

    let images = [];
    let current = 0;

    const galeriaImgs = document.querySelectorAll('.galeria-feed img');
    galeriaImgs.forEach(function(img, index) {
        images.push(img.dataset.src || img.src);
        img.style.cursor = 'pointer';
        img.addEventListener('click', function() {
            current = index;
            openLightbox();
        });
    });

    function openLightbox() {
        lightboxImg.src = images[current];
        lightbox.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        updateNav();
    }

    function closeLightbox() {
        lightbox.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function updateNav() {
        prevBtn.style.display = current === 0 ? 'none' : 'flex';
        nextBtn.style.display = current === images.length - 1 ? 'none' : 'flex';
    }

    prevBtn.addEventListener('click', function() {
        if (current > 0) { current--; lightboxImg.src = images[current]; updateNav(); }
    });

    nextBtn.addEventListener('click', function() {
        if (current < images.length - 1) { current++; lightboxImg.src = images[current]; updateNav(); }
    });

    closeBtn.addEventListener('click', closeLightbox);
    overlay.addEventListener('click', closeLightbox);

    document.addEventListener('keydown', function(e) {
        if (!lightbox.classList.contains('is-open')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft' && current > 0) { current--; lightboxImg.src = images[current]; updateNav(); }
        if (e.key === 'ArrowRight' && current < images.length - 1) { current++; lightboxImg.src = images[current]; updateNav(); }
    });
}

// ── EQUIPE HOVER LOOP ──
document.querySelectorAll('.equipe-card').forEach(function(card) {
    const extras = card.querySelectorAll('.equipe-foto-extra');
    if (!extras.length) return;

    let current = 0;
    let interval = null;

    extras[0].classList.add('ativa');

    card.addEventListener('mouseenter', function() {
        interval = setInterval(function() {
            extras[current].classList.remove('ativa');
            current = (current + 1) % extras.length;
            extras[current].classList.add('ativa');
        }, 600);
    });

    card.addEventListener('mouseleave', function() {
        clearInterval(interval);
        extras.forEach(f => f.classList.remove('ativa'));
        current = 0;
        extras[0].classList.add('ativa');
    });
});

    // ── MENU MOBILE ──
    const toggle   = document.getElementById('menuToggle');
    const overlay  = document.getElementById('menuMobile');
    const wpBtn    = document.querySelector('.wp-block-navigation__responsive-container-open');

    if (wpBtn) wpBtn.style.display = 'none';

    function openMenu() {
        toggle.classList.add('is-open');
        overlay.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        toggle.classList.remove('is-open');
        overlay.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    toggle.addEventListener('click', function() {
        overlay.classList.contains('is-open') ? closeMenu() : openMenu();
    });

    overlay.querySelectorAll('.menu-mobile-link').forEach(function(link) {
        link.addEventListener('click', closeMenu);
    });

    // ── SCROLL: header com background ──
    const header = document.querySelector('header.wp-block-template-part');
    if (header) {
        const onScroll = () => {
            if (window.scrollY > 60) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
    }

    // ── FILTRO DROPDOWN MOBILE ──
    const filtroDropdown = document.getElementById('filtroDropdown');
    if (filtroDropdown) {
        const trigger = filtroDropdown.querySelector('.filtro-dropdown-trigger');
        trigger.addEventListener('click', function() {
            filtroDropdown.classList.toggle('is-open');
        });

        document.addEventListener('click', function(e) {
            if (!filtroDropdown.contains(e.target)) {
                filtroDropdown.classList.remove('is-open');
            }
        });
    }

    // ── CARROSSEL CATÁLOGO ──
    const grid = document.querySelector('.catalogo-grid');
    if (grid) {
        const isMobile = () => window.innerWidth <= 768;

        const updateScale = () => {
            if (!isMobile()) {
                grid.querySelectorAll('.wp-block-column').forEach(col => {
                    col.style.transform = '';
                    col.style.opacity = '';
                });
                return;
            }
            const cols = grid.querySelectorAll('.wp-block-column');
            const center = grid.scrollLeft + grid.offsetWidth / 2;
            cols.forEach(col => {
                const colCenter = col.offsetLeft + col.offsetWidth / 2;
                const dist = Math.abs(center - colCenter);
                const maxDist = grid.offsetWidth * 0.6;
                const scale = Math.max(0.88, 1 - (dist / maxDist) * 0.12);
                const opacity = Math.max(0.5, 1 - (dist / maxDist) * 0.5);
                col.style.transform = `scale(${scale})`;
                col.style.opacity = opacity;
            });
        };

        grid.addEventListener('scroll', updateScale, { passive: true });
        window.addEventListener('resize', updateScale, { passive: true });
        updateScale();

        setTimeout(() => {
            if (isMobile()) {
                const cols = grid.querySelectorAll('.wp-block-column');
                if (cols.length >= 2) {
                    const middleCol = cols[1];
                    const scrollTo = middleCol.offsetLeft - (grid.offsetWidth / 2) + (middleCol.offsetWidth / 2);
                    grid.scrollLeft = scrollTo;
                }
            }
            updateScale();
        }, 100);
    }

});
</script>
<?php
});