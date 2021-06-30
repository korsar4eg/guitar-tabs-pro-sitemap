<?php 

 public function sitemap()
    {
        $mapIndex = 0;


        $f = fopen(BASE_PATH . '/public/sitemap.xml', 'w');

        fwrite($f, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
        fwrite($f, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">");
        fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/</loc><changefreq>hourly</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");
        fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/contact-us</loc><changefreq>monthly</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");
        fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/found-error</loc><changefreq>monthly</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");
        fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/how-to-open-gtp</loc><changefreq>monthly</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");
        fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/artists/alphabet/0-9</loc><changefreq>daily</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");

        $i = 5;

        foreach(range('A','Z') as $l)
        {
            fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/artists/alphabet/$l</loc><changefreq>daily</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");
            $i++;
        }


        for ($l=192; $l<=223; $l++) {
            if ($l === 218){
                continue;
            }
            fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/artists/alphabet/".iconv('CP1251', 'UTF-8', chr($l))."</loc><changefreq>daily</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");
            $i++;
        }


        $crudArtists = new ArtistsCRUD(MysqlQueryBuilder::getInstance());
       
        $offset = 0;
        do {
            
            
            MysqlQueryBuilder::getInstance()->limit(100, $offset);
            $artistData = $crudArtists->getAll();

                foreach ($artistData as $k => $artistRow){
                    $artist = ArtistsMeta::factory($artistRow);
                    fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/artist/".urlencode(htmlspecialchars($artist->getTranslit_name()))."</loc><changefreq>hourly</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");
                    $i++;

                    if ($i > self::MAX_SITE_MAP_URLS){
                        fwrite($f, "</urlset>");
                        fclose($f);

                        $mapIndex++;

                        $f = fopen(BASE_PATH . '/public/sitemap'.$mapIndex.'.xml', 'w');
                        fwrite($f, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
                        fwrite($f, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">");

                        $i = 0;
                    }
                }

                $offset += 100;

        } while (count($artistData) > 0);

        $artistData = null;
        $crudArtists = null;
        $artist = null;
        $offset = 0;

        $crudCompositions = new CompositionsCRUD(MysqlQueryBuilder::getInstance());

        do {

        MysqlQueryBuilder::getInstance()->where(CompositionsMeta::FIELD_ENABLED, 1)->limit(100, $offset);
        $compositionsData = $crudCompositions->getAll();

        foreach ($compositionsData as $kk => $row){
            $composition = CompositionsMeta::factory($row);
            fwrite($f, "<url><loc>https://".SERVER_DOMAIN."/composition/".urlencode(htmlspecialchars($composition->getCompNameTranslit()))."_". $composition->getId() ."</loc><changefreq>hourly</changefreq> <lastmod>".date('Y-m-d')."</lastmod></url>");
            $i++;

            if ($i > self::MAX_SITE_MAP_URLS){
                fwrite($f, "</urlset>");
                fclose($f);

                $mapIndex++;

                $f = fopen(BASE_PATH . '/public/sitemap'.$mapIndex.'.xml', 'w');
                fwrite($f, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
                fwrite($f, "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">");

                $i = 0;
            }
        }

        $offset += 100;

    } while(count($compositionsData) > 0);

        $compositionsData = null;
        $composition = null;
        $crudCompositions = null;

        fwrite($f, "</urlset>");
        fclose($f);

    }
