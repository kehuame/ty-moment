<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

<div class="aside" id="single-sidebar" style="height: auto !important;">
    <?php 
    // 显示更多相关文章（同分类下的其他文章，排除当前文章）
    $db = \Typecho\Db::get();
    $currentCategoryMids = array();
    $postCategories = $db->fetchAll($db->select('table.metas.mid')
        ->from('table.metas')
        ->join('table.relationships', 'table.metas.mid = table.relationships.mid')
        ->where('table.relationships.cid = ?', $this->cid)
        ->where('table.metas.type = ?', 'category'));
    
    foreach ($postCategories as $cat) {
        $currentCategoryMids[] = $cat['mid'];
    }
    
    if (!empty($currentCategoryMids)):
        // 构建安全的IN查询 - 使用intval确保安全
        $safeMids = array_map('intval', $currentCategoryMids);
        $midPlaceholders = implode(',', $safeMids);
        
        // 获取同分类下的所有文章（排除当前文章）
        $allRelatedPosts = $db->fetchAll($db->select()->from('table.contents')
            ->join('table.relationships', 'table.contents.cid = table.relationships.cid')
            ->where('table.contents.type = ?', 'post')
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.cid != ?', $this->cid)
            ->where('table.relationships.mid IN (' . $midPlaceholders . ')')
            ->group('table.contents.cid'));
        
        // 随机打乱并取前4篇
        shuffle($allRelatedPosts);
        $relatedPosts = array_slice($allRelatedPosts, 0, 4);
        
        if (!empty($relatedPosts)):
    ?>
    <div class="aside-block block-wrap">
        <div class="single-relative">
            <h2 class="block-title">更多相关文章</h2>
            <div class="aside-post-list">
                <?php foreach ($relatedPosts as $postRow): 
                    $postWidget = \Widget\Archive::allocWithAlias('related_' . $postRow['cid'], 'pageSize=1&type=post', 'cid=' . $postRow['cid']);
                    if ($postWidget->have()):
                        $postWidget->next();
                        
                        // 获取分类
                        $relatedCategories = $db->fetchAll($db->select('table.metas.name', 'table.metas.slug', 'table.metas.mid')
                            ->from('table.metas')
                            ->join('table.relationships', 'table.metas.mid = table.relationships.mid')
                            ->where('table.relationships.cid = ?', $postRow['cid'])
                            ->where('table.metas.type = ?', 'category'));
                ?>
                <div class="post-item">
                    <div class="post-item-cover">
                        <a class="post-item-img" href="<?php $postWidget->permalink(); ?>" title="<?php $postWidget->title(); ?>" >
                            <?php if ($postWidget->fields->cover): ?>
                                <img class="hover-scale" src="<?php echo ensureHttpsUrl($postWidget->fields->cover); ?>" alt="<?php $postWidget->title(); ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background-color: #F6F8FF; display: flex; align-items: center; justify-content: center; color: #9DA0B3;">无图片</div>
                            <?php endif; ?>
                        </a>
                        <ul class="post-categories">
                            <?php 
                            if (!empty($relatedCategories)):
                                foreach ($relatedCategories as $cat):
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
                    <a href="<?php $postWidget->permalink(); ?>" class="post-item-title" title="<?php $postWidget->title(); ?>" >
                        <h3><?php $postWidget->title(); ?></h3>
                    </a>
                    <div class="post-item-footer">
                        <div class="tag-wrap">
                            <?php 
                            // 获取标签
                            $relatedTags = $db->fetchAll($db->select('table.metas.name', 'table.metas.slug')
                                ->from('table.metas')
                                ->join('table.relationships', 'table.metas.mid = table.relationships.mid')
                                ->where('table.relationships.cid = ?', $postRow['cid'])
                                ->where('table.metas.type = ?', 'tag')
                                ->limit(5));
                            if (!empty($relatedTags)):
                                foreach ($relatedTags as $tag):
                            ?>
                            <a href="<?php echo \Typecho\Common::url('tag/' . urlencode($tag['slug']), $this->options->index); ?>" rel="tag"><?php echo htmlspecialchars($tag['name']); ?></a>
                            <?php 
                                endforeach;
                            endif;
                            ?>
                        </div>
                        <div class="post-item-meta"><?php echo date('Y-m-d', $postRow['created']); ?></div>
                    </div>
                    <p class="post-item-summary"><?php 
                        $excerpt = $postRow['text'];
                        // 移除 HTML 标签
                        $excerpt = strip_tags($excerpt);
                        // 移除 Markdown 格式的图片 ![alt](url)
                        $excerpt = preg_replace('/!\[.*?\]\(.*?\)/', '', $excerpt);
                        // 移除 Markdown 格式的图片引用 ![alt][ref]
                        $excerpt = preg_replace('/!\[[^\]]*\]\[[^\]]+\]/', '', $excerpt);
                        // 移除图片引用定义 [ref]: url
                        $excerpt = preg_replace('/\[[^\]]+\]:\s*\S+/', '', $excerpt);
                        // 移除纯 URL（http:// 或 https:// 开头的）
                        $excerpt = preg_replace('/https?:\/\/[^\s]+/', '', $excerpt);
                        // 清理多余的空行和空白字符
                        $excerpt = preg_replace('/\n\s*\n/', "\n", $excerpt);
                        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
                        $excerpt = trim($excerpt);
                        echo \Typecho\Common::subStr($excerpt, 0, 100, '...');
                    ?></p>
                </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </div>
    <?php 
        endif;
        endif;
    ?>
</div>

