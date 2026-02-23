<?php
/**
 * 这是一款三栏简约博客主题 - 刻画kehua.me
 *
 * @package moment - 此刻主题
 * @author 刻画kehua.me
 * @version 1.0.0
 * @link https://kehua.me
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

function themeConfig($form)
{
    $logoUrl = new \Typecho\Widget\Helper\Form\Element\Text(
        'logoUrl',
        null,
        null,
        _t('站点 LOGO 地址'),
        _t('在这里填入一个图片 URL 地址, 以在网站标题前加上一个 LOGO')
    );

    $form->addInput($logoUrl);
    
    $sliderPosts = new \Typecho\Widget\Helper\Form\Element\Text(
        'sliderPosts',
        null,
        null,
        _t('首页幻灯片文章ID'),
        _t('填写要在首页幻灯片显示的文章ID，多个ID用英文逗号分隔，例如：1,2,3,4,5')
    );

    $form->addInput($sliderPosts);
    
    $navMenu = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'navMenu',
        null,
        null,
        _t('左侧导航菜单'),
        _t('每行一个导航项，格式：链接文字|链接地址。例如：首页|/ 或 关于|/about.html 或 分类|/category/travel.html。留空则不显示任何菜单项')
    );
    $form->addInput($navMenu);
    
    $footerCopyright = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'footerCopyright',
        null,
        null,
        _t('底部版权信息'),
        _t('自定义底部版权信息，支持 HTML 代码。留空则只显示默认版权信息。')
    );
    $form->addInput($footerCopyright);
    
    $sidebarBlockTitle = new \Typecho\Widget\Helper\Form\Element\Text(
        'sidebarBlockTitle',
        null,
        '推荐内容',
        _t('首页右侧推荐板块标题'),
        _t('设置首页右侧顶部推荐板块的标题')
    );
    $form->addInput($sidebarBlockTitle);
    
    $sidebarBlockItems = new \Typecho\Widget\Helper\Form\Element\Textarea(
        'sidebarBlockItems',
        null,
        null,
        _t('首页右侧推荐板块内容'),
        _t('每行一个推荐项，格式：标题|缩略图URL|链接地址。最多3个，例如：<br>精选文章|https://example.com/image1.jpg|https://example.com/post1<br>热门推荐|https://example.com/image2.jpg|https://example.com/post2')
    );
    $form->addInput($sidebarBlockItems);
}

/**
 * 文章自定义字段
 * 在文章编辑页面显示自定义字段输入框
 */
function themeFields($layout)
{
    // 文章缩略图
    $cover = new \Typecho\Widget\Helper\Form\Element\Text(
        'cover',
        null,
        null,
        _t('文章缩略图'),
        _t('填写文章缩略图的完整 URL 地址，用于在列表页显示')
    );
    $layout->addItem($cover);
}

/**
 * 将HTTP图片URL转换为HTTPS，避免混合内容警告
 * @param string $url 图片URL
 * @return string 转换后的URL
 */
function ensureHttpsUrl($url) {
    if (empty($url)) {
        return $url;
    }
    
    // 如果是HTTP协议，转换为HTTPS
    if (preg_match('/^http:\/\//i', $url)) {
        $url = preg_replace('/^http:\/\//i', 'https://', $url);
    }
    
    return $url;
}

/**
 * 阅读统计
 * 在文章页面调用此函数来统计阅读次数
 * 调用方式：<?php getPostViews($this); ?>
 */
function getPostViews($archive)
{
    $cid = $archive->cid;
    $db = \Typecho\Db::get();
    
    // 检查 views 字段是否存在，如果不存在则创建
    $tableInfo = $db->fetchAll($db->query('SHOW COLUMNS FROM `' . $db->getPrefix() . 'contents`'));
    $tableInfo = array_column($tableInfo, 'Field', 'Field');
    if (!array_key_exists('views', $tableInfo)) {
        $db->query('ALTER TABLE `' . $db->getPrefix() . 'contents` ADD `views` INT(10) DEFAULT 0;');
    }
    
    // 获取当前阅读次数
    $views = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    $exist = isset($views['views']) ? (int)$views['views'] : 0;
    
    // 如果是文章页面，则增加阅读次数（通过 Cookie 防止重复统计）
    if ($archive->is('single')) {
        $cookie = \Typecho\Cookie::get('contents_views');
        $viewedPosts = $cookie ? explode(',', $cookie) : [];
        if (!in_array($cid, $viewedPosts)) {
            $db->query($db->update('table.contents')
                ->rows(array('views' => $exist + 1))
                ->where('cid = ?', $cid));
            $viewedPosts[] = $cid;
            $cookie = implode(',', $viewedPosts);
            \Typecho\Cookie::set('contents_views', $cookie);
        }
    }
}

/**
 * 获取文章的阅读次数（不增加计数）
 * 用于在列表页显示阅读次数
 * 调用方式：<?php echo getPostViewsCount($postCid); ?>
 */
function getPostViewsCount($cid)
{
    $db = \Typecho\Db::get();
    
    // 检查 views 字段是否存在
    $tableInfo = $db->fetchAll($db->query('SHOW COLUMNS FROM `' . $db->getPrefix() . 'contents`'));
    $tableInfo = array_column($tableInfo, 'Field', 'Field');
    if (!array_key_exists('views', $tableInfo)) {
        return 0;
    }
    
    $views = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    return isset($views['views']) ? (int)$views['views'] : 0;
}

