<?php

return [

    '/index' => '///',

    '/news/topics/<1>' => '/News/Index/NewsByTopic(id=<1>)',
    '/news/<1>' => '/News/Index/Story(id=<1>)',

    '/pages/<3>/<2>/<1>' => '/Pages/Index/PageByUrl(url=<1>)',
    '/pages/<2>/<1>' => '/Pages/Index/PageByUrl(url=<1>)',
    '/pages/<1>' => '/Pages/Index/PageByUrl(url=<1>)',

    '/gallery/albums/<1>' => '/Gallery/Index/AlbumByUrl(url=<1>)',

    '/documents/categories/<1>' => '/Documents/Index/DocumentsByCategory(id=<1>)',
    '/documents/<1>' => '/Documents/Index/Document(id=<1>)',

];