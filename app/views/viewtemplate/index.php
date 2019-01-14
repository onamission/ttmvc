<?php require APPROOT . '/views/inc/header.php'; ?>
<?php flash('post_message'); ?>
<div class="row mb-3">
    <div class="col-md-6">
    <h1>Posts</h1>
    </div>
    <div class="col-md-6">
        <a class="btn btn-primary pull-right" 
           href="<?php echo URLROOT; ?>/posts/add"><i 
                class="fa fa-pencil" aria-hidden="true"></i> Add Post
        </a>
    </div>
</div>
<table class="table table-striped table-border">
    <thead>
        <tr>
            <th>Field1</th>
            <th>Field2</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
<?php foreach($data['~record~'] as $record) : ?>
    <tr>
        <td><?php echo $record->field1; ?></td>
        <td><?php echo $record->field2; ?></td>
        <td>
            <a 
               href="<?php echo URLROOT; ?>/~record~/detail/<?php echo $record->postId; ?>"
               title="View Details">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </a>
            <a 
               href="<?php echo URLROOT; ?>/~record~/edit/<?php echo $record->postId; ?>"
               title="Edit">
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </a>
            <a 
               href="<?php echo URLROOT; ?>/~record~/delete/<?php echo $record->postId; ?>"
               title="Delete">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </a>
        </td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php require APPROOT . '/views/inc/footer.php'; ?>