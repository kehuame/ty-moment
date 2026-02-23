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
$this->need('header.php');
?>

<div class="main" style="height: auto !important;">
    <?php 
    // 首页幻灯片
    if ($this->is('index')):
        $sliderPostIds = $this->options->sliderPosts;
        if ($sliderPostIds):
            $sliderIds = explode(',', $sliderPostIds);
            $sliderIds = array_map('trim', $sliderIds);
            $sliderIds = array_filter($sliderIds);
            if (!empty($sliderIds)):
    ?>
    <div class="pic-cover-list slider-container">
        <div class="slider has-touch">
            <?php foreach ($sliderIds as $sliderId): 
                $sliderPost = \Widget\Archive::allocWithAlias('slider_' . intval($sliderId), 'pageSize=1&type=post', 'cid=' . intval($sliderId));
                if ($sliderPost->have()):
                    $sliderPost->next();
            ?>
            <a href="<?php $sliderPost->permalink(); ?>" class="pic-cover-item slider__item" >
                <?php if ($sliderPost->fields->cover): ?>
                    <img src="<?php echo ensureHttpsUrl($sliderPost->fields->cover); ?>" alt="<?php $sliderPost->title(); ?>" class="pic-cover-item-img" draggable="false">
                <?php else: ?>
                    <div style="width: 100%; height: 405px; background-color: #F6F8FF; display: flex; align-items: center; justify-content: center; color: #9DA0B3;">无图片</div>
                <?php endif; ?>
                <div class="slider__caption">
                    <h3 class="pic-cover-item-title"><?php $sliderPost->title(); ?></h3>
                </div>
            </a>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
        <div class="slider__switch slider__switch--prev" data-ikslider-dir="prev">
            <span><svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M13.89 17.418c.27.272.27.71 0 .98s-.7.27-.968 0l-7.83-7.91c-.268-.27-.268-.706 0-.978l7.83-7.908c.268-.27.7-.27.97 0s.267.71 0 .98L6.75 10l7.14 7.418z"></path>
            </svg></span>
        </div>
        <div class="slider__switch slider__switch--next" data-ikslider-dir="next">
            <span><svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 20 20">
                <path d="M13.25 10L6.11 2.58c-.27-.27-.27-.707 0-.98.267-.27.7-.27.968 0l7.83 7.91c.268.27.268.708 0 .978l-7.83 7.908c-.268.27-.7.27-.97 0s-.267-.707 0-.98L13.25 10z"></path>
            </svg></span>
        </div>
    </div>
    <?php 
            endif;
        endif;
    endif;
    ?>
    
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

