<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="Cache-Control" content="no-siteapp">
    <meta name="renderer" content="webkit">
    <meta name="format-detection" content="telephone=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <title><?php $this->archiveTitle([
            'category' => _t('分类 %s 下的文章'),
            'search'   => _t('包含关键字 %s 的文章'),
            'tag'      => _t('标签 %s 下的文章'),
            'author'   => _t('%s 发布的文章')
        ], '', ' - '); ?><?php $this->options->title(); ?></title>
    
    <meta name="description" content="<?php $this->archiveDescription('', '', ''); ?><?php $this->options->description(); ?>">
    <meta name="keywords" content="<?php $this->archiveKeywords('', '', ''); ?>">
    
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/style.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/slider.css'); ?>">
    
    <?php $this->header(); ?>
</head>
<body class="<?php if ($this->is('index')): ?>home blog<?php elseif ($this->is('post')): ?>post-template-default single single-post<?php elseif ($this->is('category')): ?>archive category<?php endif; ?>" style="">
<div class="box-wrap" style="height: auto !important;">

    <div class="header">
        <div class="logo-wrap">
            <a href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title(); ?>" class="logo-img-wrap">
                <?php if ($this->options->logoUrl): ?>
                    <img src="<?php echo ensureHttpsUrl($this->options->logoUrl); ?>" alt="<?php $this->options->title(); ?> LOGO" class="logo">
                <?php endif; ?>
                <h1><?php $this->options->title(); ?></h1>
            </a>
            <div class="sub-title"><?php $this->options->description(); ?></div>
        </div>

        <div class="menu-header-container">
            <?php
            $navMenu = $this->options->navMenu;
            if (!empty($navMenu)):
            ?>
            <ul id="menu-header" class="menu">
                <?php
                $menuItems = explode("\n", $navMenu);
                foreach ($menuItems as $item):
                    $item = trim($item);
                    if (!empty($item)):
                        $parts = explode('|', $item, 2);
                        $text = isset($parts[0]) ? trim($parts[0]) : '';
                        $url = isset($parts[1]) ? trim($parts[1]) : '/';
                        if (!empty($text)):
                            // 处理相对URL
                            if (!preg_match('/^(https?:\/\/|\/)/', $url)) {
                                $url = $this->options->siteUrl . $url;
                            }
                            // 判断当前页面是否激活
                            $isCurrent = false;
                            $currentUrl = $this->request->getRequestUrl();
                            $menuUrl = parse_url($url, PHP_URL_PATH);
                            $currentPath = parse_url($currentUrl, PHP_URL_PATH);
                            
                            // 标准化路径（去除尾部斜杠）
                            $menuUrl = rtrim($menuUrl ?: '/', '/');
                            $currentPath = rtrim($currentPath ?: '/', '/');
                            
                            // 首页判断
                            if ($this->is('index')) {
                                if ($menuUrl == '' || $menuUrl == '/' || $url == $this->options->siteUrl || $url == rtrim($this->options->siteUrl, '/') . '/') {
                                    $isCurrent = true;
                                }
                            } else {
                                // 精确匹配当前路径
                                if ($menuUrl && $currentPath && $menuUrl === $currentPath) {
                                    $isCurrent = true;
                                }
                            }
                ?>
                <li class="menu-item<?php if ($isCurrent): ?> current-menu-item<?php endif; ?>">
                    <a href="<?php echo htmlspecialchars($url); ?>" title="<?php echo htmlspecialchars($text); ?>"><?php echo htmlspecialchars($text); ?></a>
                </li>
                <?php
                        endif;
                    endif;
                endforeach;
                ?>
            </ul>
            <?php endif; ?>
        </div>
    </div>

