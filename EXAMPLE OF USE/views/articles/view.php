<h1>Article <?php echo Yii::t($model, 'title'); ?></h1>
<div class="article-info" style="float:right;">
	<strong>Author: <?php echo Yii::t($model, 'author'); ?></strong>
	<i>Create date: <?php echo Yii::t($model, 'createDate'); ?></i>
	<i>Last update: <?php echo Yii::t($model, 'updateDate'); ?></i>
</div>
<div class="intro-text">
	<p><?php echo Yii::t($model, 'introText'); ?></p>
</div>
<div class="full-text">
	<p><?php echo Yii::t($model, 'fullText'); ?></p>
</div>