<?php require APPROOT . '/views/inc/header.php'; ?>
<a href="<?php echo URLROOT; ?>" class="btn btn-light mb-3">
    <i class="fa fa-backward" aria-hidden="true"></i> Back
</a>
<br>
<h1><?php echo $data['~record~']->field1; ?></h1>
<p><?php echo $data['~record~']->field2; ?></p>
<?php if($data['~record~']->user_id == $_SESSION['user_id']) : ?>
  <hr>
  <a class="btn btn-dark"
     href="<?php echo URLROOT; ?>/~record~/edit/<?php echo $data['~record~']->id; ?>">Edit
  </a>

    <form class="float-sm-right"
        action="<?php echo URLROOT; ?>/people/delete/<?php echo $data['people']->id; ?>"
        method="post">
    <input type="submit" class="btn btn-danger" value="Delete">
    </form>
<?php endif; ?>
<?php require APPROOT . '/views/inc/footer.php'; ?>