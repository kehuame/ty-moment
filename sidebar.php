<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div class="aside" style="height: auto !important;">
    <?php if ($this->is('category')): ?>
        <?php 
        // 分类页面：显示本栏目热门文章
        $db = \Typecho\Db::get();
        $currentCategoryMid = null;
        // 方法1: 尝试从archive对象获取mid
        if (method_exists($this, 'getArchiveSlug')) {
            $currentCategorySlug = $this->getArchiveSlug();
            $categoryMeta = $db->fetchRow($db->select('mid')->from('table.metas')
                ->where('slug = ?', $currentCategorySlug)
                ->where('type = ?', 'category')
                ->limit(1));
            if ($categoryMeta) {
                $currentCategoryMid = $categoryMeta['mid'];
            }
        }
        // 方法2: 如果方法1失败，尝试从category对象获取
        if (!$currentCategoryMid && isset($this->category)) {
            $currentCategoryMid = $this->category->mid;
        }
        
        if ($currentCategoryMid):
            // 检查 views 字段是否存在
            $tableInfo = $db->fetchAll($db->query('SHOW COLUMNS FROM `' . $db->getPrefix() . 'contents`'));
            $tableInfo = array_column($tableInfo, 'Field', 'Field');
            $hasViews = array_key_exists('views', $tableInfo);
            
            if ($hasViews) {
                // 按阅读量降序排列
                $categoryHotPosts = $db->fetchAll($db->select()->from('table.contents')
                    ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
                    ->where('table.contents.type = ?', 'post')
                    ->where('table.contents.status = ?', 'publish')
                    ->where('table.relationships.mid = ?', $currentCategoryMid)
                    ->order('table.contents.views', \Typecho\Db::SORT_DESC)
                    ->limit(5));
            } else {
                // 如果没有 views 字段，按发布时间排序
                $categoryHotPosts = $db->fetchAll($db->select()->from('table.contents')
                    ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
                    ->where('table.contents.type = ?', 'post')
                    ->where('table.contents.status = ?', 'publish')
                    ->where('table.relationships.mid = ?', $currentCategoryMid)
                    ->order('table.contents.created', \Typecho\Db::SORT_DESC)
                    ->limit(5));
            }
            
            if (!empty($categoryHotPosts)):
        ?>
        <div class="aside-block">
            <h2 class="block-title">
                <i class="tficon icon-fire-line"></i> 本栏目热门文章
            </h2>
            <div class="sidebar-post-list">
                <?php foreach ($categoryHotPosts as $postRow): 
                    $postWidget = \Widget\Archive::allocWithAlias('cat_hot_' . $postRow['cid'], 'pageSize=1&type=post', 'cid=' . $postRow['cid']);
                    if ($postWidget->have()):
                        $postWidget->next();
                ?>
                <div class="sider-post-item">
                    <a class="sider-post-item-img" href="<?php $postWidget->permalink(); ?>" title="<?php $postWidget->title(); ?>">
                        <?php if ($postWidget->fields->cover): ?>
                            <img class="hover-scale" src="<?php echo ensureHttpsUrl($postWidget->fields->cover); ?>" alt="<?php $postWidget->title(); ?>">
                        <?php else: ?>
                            <div style="width: 128px; height: 104px; background-color: #F6F8FF; display: flex; align-items: center; justify-content: center; color: #9DA0B3; font-size: 12px; border-radius: 16px;">无图片</div>
                        <?php endif; ?>
                    </a>
                    <a class="sider-post-item-title" href="<?php $postWidget->permalink(); ?>" title="<?php $postWidget->title(); ?>">
                        <h3><?php $postWidget->title(); ?></h3>
                    </a>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
        <?php 
            endif;
        endif;
    else: 
        // 首页：显示原来的侧边栏内容
    ?>
    <?php if (!$this->is('search')): ?>
    <div class="aside-block">
        <div class="top-bar">
            <form class="search-form" method="get" action="<?php $this->options->siteUrl(); ?>">
                <button class="tficon icon-search" type="submit"></button>
                <input type="text" name="s" class="search-input" placeholder="输入关键词，回车搜索" value="<?php echo htmlspecialchars($this->request->get('s', '')); ?>">
            </form>
        </div>
        
        <?php 
        // 首页右侧推荐板块（从主题设置读取）
        $sidebarBlockTitle = $this->options->sidebarBlockTitle;
        $sidebarBlockItems = $this->options->sidebarBlockItems;
        
        if ($sidebarBlockItems):
            $items = explode("\n", trim($sidebarBlockItems));
            $items = array_filter(array_map('trim', $items));
            $items = array_slice($items, 0, 3); // 最多3个
            
            if (!empty($items)):
        ?>
        <div class="block-wrap">
            <h2 class="block-title"><?php echo htmlspecialchars($sidebarBlockTitle ?: '推荐内容'); ?></h2>
            <div class="photo-list">
                <?php 
                foreach ($items as $item):
                    $parts = explode('|', $item);
                    if (count($parts) >= 3):
                        $itemTitle = trim($parts[0]);
                        $itemImage = trim($parts[1]);
                        $itemLink = trim($parts[2]);
                ?>
                <a href="<?php echo htmlspecialchars($itemLink); ?>" title="<?php echo htmlspecialchars($itemTitle); ?>" class="photo-item" target="_blank">
                    <?php if ($itemImage): ?>
                        <img src="<?php echo ensureHttpsUrl($itemImage); ?>" alt="<?php echo htmlspecialchars($itemTitle); ?>" class="photo-item-img hover-scale">
                    <?php else: ?>
                        <div style="width: 100%; height: 100%; background-color: #F6F8FF; display: flex; align-items: center; justify-content: center; color: #9DA0B3; font-size: 12px;">无图片</div>
                    <?php endif; ?>
                    <div class="photo-item-inner">
                        <h3 class="photo-item-title"><?php echo htmlspecialchars($itemTitle); ?></h3>
                    </div>
                </a>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
        <?php 
            endif;
        endif; 
        ?>
    </div>
    <?php endif; ?>
    
    <?php 
    // 显示热门文章（按阅读量排序）
    $db = \Typecho\Db::get();
    
    // 检查 views 字段是否存在
    $tableInfo = $db->fetchAll($db->query('SHOW COLUMNS FROM `' . $db->getPrefix() . 'contents`'));
    $tableInfo = array_column($tableInfo, 'Field', 'Field');
    $hasViews = array_key_exists('views', $tableInfo);
    
    if ($hasViews) {
        // 按阅读量降序排列
        $hotPosts = $db->fetchAll($db->select()->from('table.contents')
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.status = ?', 'publish')
            ->order('table.contents.views', \Typecho\Db::SORT_DESC)
            ->limit(5));
    } else {
        // 如果没有 views 字段，按发布时间排序
        $hotPosts = $db->fetchAll($db->select()->from('table.contents')
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.status = ?', 'publish')
            ->order('table.contents.created', \Typecho\Db::SORT_DESC)
            ->limit(5));
    }
    
    if (!empty($hotPosts)):
    ?>
    <div class="aside-block block-wrap">
        <h2 class="block-title">
            <a href="<?php $this->options->siteUrl(); ?>">热门文章<i class="tficon icon-right"></i></a>
        </h2>
        <div class="sidebar-post-list">
            <?php foreach ($hotPosts as $postRow): 
                $postWidget = \Widget\Archive::allocWithAlias('hot_post_' . $postRow['cid'], 'pageSize=1&type=post', 'cid=' . $postRow['cid']);
                if ($postWidget->have()):
                    $postWidget->next();
            ?>
            <div class="sider-post-item">
                <a class="sider-post-item-img" href="<?php $postWidget->permalink(); ?>" title="<?php $postWidget->title(); ?>">
                    <?php if ($postWidget->fields->cover): ?>
                        <img class="hover-scale" src="<?php echo ensureHttpsUrl($postWidget->fields->cover); ?>" alt="<?php $postWidget->title(); ?>">
                    <?php else: ?>
                        <div style="width: 128px; height: 104px; background-color: #F6F8FF; display: flex; align-items: center; justify-content: center; color: #9DA0B3; font-size: 12px; border-radius: 16px;">无图片</div>
                    <?php endif; ?>
                </a>
                <a class="sider-post-item-title" href="<?php $postWidget->permalink(); ?>" title="<?php $postWidget->title(); ?>">
                    <h3><?php $postWidget->title(); ?></h3>
                </a>
            </div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

