<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>
<?php 
// 统计阅读次数
getPostViews($this);
?>

<div class="main" style="height: auto !important;">
    <div class="top-bar">
        <div class="crumbs">
            <a href="<?php $this->options->siteUrl(); ?>">首页</a>
            <?php $this->category(' '); ?>
            <span>本文内容</span>
        </div>
        <form class="search-form" method="get" action="<?php $this->options->siteUrl(); ?>">
            <button class="tficon icon-search" type="submit"></button>
            <input type="text" name="s" class="search-input" placeholder="输入关键词，回车搜索" value="">
        </form>
    </div>

    <div class="post-wrap" id="post-wrap" style="height: auto !important;">
        <div class="post-header">
            <h1 class="post-title"><?php $this->title(); ?></h1>
            <div class="post-meta">
                <ul class="post-categories">
                    <?php 
                    // 手动构建分类HTML以确保样式正确应用
                    $db = \Typecho\Db::get();
                    $postCategories = $db->fetchAll($db->select('table.metas.name', 'table.metas.slug', 'table.metas.mid')
                        ->from('table.metas')
                        ->join('table.relationships', 'table.metas.mid = table.relationships.mid')
                        ->where('table.relationships.cid = ?', $this->cid)
                        ->where('table.metas.type = ?', 'category'));
                    
                    if (!empty($postCategories)):
                        foreach ($postCategories as $cat):
                            $catWidget = \Widget\Metas\Category\Rows::alloc();
                            $catWidget->mid = $cat['mid'];
                            $catWidget->to($catWidget);
                            if ($catWidget->have()):
                                $catWidget->next();
                    ?>
                    <li><a href="<?php $catWidget->permalink(); ?>" rel="category tag"><?php $catWidget->name(); ?></a></li>
                    <?php 
                            endif;
                        endforeach;
                    endif;
                    ?>
                </ul>
                <?php $this->date('Y-m-d'); ?>
                <?php $views = getPostViewsCount($this->cid); if ($views > 0): ?>
                <span class="post-views"><?php echo $views; ?> 次阅读</span>
                <?php endif; ?>
                <div class="tag-wrap post-header-tags">
                    <?php $this->tags(' ', true, ''); ?>
                </div>
            </div>
        </div>

        <div class="post-content" id="post-content">
            <?php $this->content(); ?>
        </div>

        <div class="post-list">
            <?php 
            // 获取上一篇
            $db = \Typecho\Db::get();
            $prevPost = $db->fetchRow($db->select()->from('table.contents')
                ->where('table.contents.type = ?', 'post')
                ->where('table.contents.status = ?', 'publish')
                ->where('table.contents.created < ?', $this->created)
                ->order('table.contents.created', \Typecho\Db::SORT_DESC)
                ->limit(1));
            
            if ($prevPost):
                $prevWidget = \Widget\Archive::allocWithAlias('prev_post_' . $prevPost['cid'], 'pageSize=1&type=post', 'cid=' . $prevPost['cid']);
                if ($prevWidget->have()):
                    $prevWidget->next();
            ?>
            <div class="post-item">
                <div class="post-item-cover">
                    <a class="post-item-img" href="<?php $prevWidget->permalink(); ?>" title="<?php $prevWidget->title(); ?>">
                        <?php if ($prevWidget->fields->cover): ?>
                            <img class="hover-scale" src="<?php echo ensureHttpsUrl($prevWidget->fields->cover); ?>" alt="<?php $prevWidget->title(); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background-color: #F6F8FF; display: flex; align-items: center; justify-content: center; color: #9DA0B3;">无图片</div>
                        <?php endif; ?>
                        <h5 class="single-prev-text"><i class="tficon icon-left"></i> 上一篇</h5>
                    </a>
                </div>
                <a href="<?php $prevWidget->permalink(); ?>" class="post-item-title" title="<?php $prevWidget->title(); ?>">
                    <h3><?php $prevWidget->title(); ?></h3>
                </a>
            </div>
            <?php 
                endif;
            endif; ?>
            
            <?php 
            // 获取下一篇
            $nextPost = $db->fetchRow($db->select()->from('table.contents')
                ->where('table.contents.type = ?', 'post')
                ->where('table.contents.status = ?', 'publish')
                ->where('table.contents.created > ?', $this->created)
                ->order('table.contents.created', \Typecho\Db::SORT_ASC)
                ->limit(1));
            
            if ($nextPost):
                $nextWidget = \Widget\Archive::allocWithAlias('next_post_' . $nextPost['cid'], 'pageSize=1&type=post', 'cid=' . $nextPost['cid']);
                if ($nextWidget->have()):
                    $nextWidget->next();
            ?>
            <div class="post-item">
                <div class="post-item-cover">
                    <a class="post-item-img" href="<?php $nextWidget->permalink(); ?>" title="<?php $nextWidget->title(); ?>">
                        <?php if ($nextWidget->fields->cover): ?>
                            <img class="hover-scale" src="<?php echo ensureHttpsUrl($nextWidget->fields->cover); ?>" alt="<?php $nextWidget->title(); ?>">
                        <?php else: ?>
                            <div style="width: 100%; height: 100%; background-color: #F6F8FF; display: flex; align-items: center; justify-content: center; color: #9DA0B3;">无图片</div>
                        <?php endif; ?>
                        <h5 class="single-next-text"><i class="tficon icon-right"></i> 下一篇</h5>
                    </a>
                </div>
                <a href="<?php $nextWidget->permalink(); ?>" class="post-item-title" title="<?php $nextWidget->title(); ?>">
                    <h3><?php $nextWidget->title(); ?></h3>
                </a>
            </div>
            <?php 
                endif;
            endif; ?>
        </div>
    </div>
</div>

<?php $this->need('post-sidebar.php'); ?>
<?php $this->need('footer.php'); ?>

