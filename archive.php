<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="main" style="height: auto !important;">
    <div class="top-bar">
        <div class="crumbs">
            <a href="<?php $this->options->siteUrl(); ?>">首页</a>
            <h2><?php $this->archiveTitle([
                    'category' => _t('%s'),
                    'search'   => _t('包含关键字 %s 的文章'),
                    'tag'      => _t('标签 %s 下的文章'),
                    'author'   => _t('%s 发布的文章')
                ], '', ''); ?></h2>
        </div>
        <form class="search-form" method="get" action="<?php $this->options->siteUrl(); ?>">
            <button class="tficon icon-search" type="submit"></button>
            <input type="text" name="s" class="search-input" placeholder="输入关键词，回车搜索" value="<?php echo htmlspecialchars($this->request->get('s', '')); ?>">
        </form>
    </div>

    <?php if ($this->is('category')): ?>
        <ul id="menu-web" class="cat-tab-wrap">
            <li class="menu-item current-menu-item">
                <?php $this->category(',', true, ''); ?>
            </li>
        </ul>
    <?php endif; ?>

    <div class="post-list">
        <?php if ($this->have()): ?>
            <?php while ($this->next()): ?>
                <?php 
                // 获取自定义字段中的缩略图URL
                $thumbnailUrl = '';
                if ($this->fields->cover) {
                    $thumbnailUrl = $this->fields->cover;
                }
                ?>
                <div class="post-item">
                    <div class="post-item-cover">
                        <a class="post-item-img" href="<?php $this->permalink(); ?>" title="<?php $this->title(); ?>" >
                        <?php if ($thumbnailUrl): ?>
                            <img class="hover-scale" src="<?php echo htmlspecialchars(ensureHttpsUrl($thumbnailUrl)); ?>" alt="<?php $this->title(); ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background-color: #F6F8FF; display: flex; align-items: center; justify-content: center; color: #9DA0B3;">无图片</div>
                            <?php endif; ?>
                        </a>
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
                    </div>
                    <a href="<?php $this->permalink(); ?>" class="post-item-title" title="<?php $this->title(); ?>" >
                        <h3><?php $this->title(); ?></h3>
                    </a>
                    <div class="post-item-footer">
                        <div class="tag-wrap">
                            <?php $this->tags(' ', true, ''); ?>
                        </div>
                        <div class="post-item-meta"><?php $this->date('Y-m-d'); ?></div>
                    </div>
                    <p class="post-item-summary"><?php $this->excerpt(100, '...'); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="post-item">
                <p>没有找到内容</p>
            </div>
        <?php endif; ?>
    </div>

    <?php $this->pageNav('&laquo;', '&raquo;', 3, '...', array(
        'wrapTag' => 'ul',
        'wrapClass' => 'pagination',
        'itemTag' => 'li',
        'textTag' => 'span',
        'currentClass' => 'active',
        'prevClass' => 'prev-page',
        'nextClass' => 'next-page'
    )); ?>
</div>

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>

