<?php
    /*
     * Use this template to create a rough form for your app. To customize this,
     * change the field names to match your model's fields and appropraite form
     * fields for each field type.
     * 
     * Customize the UI by changing the class names to match Bootstrap or custom
     * CSS classes.
     */
?>
<div class="form-group">
    <label>Field1:<sup>*</sup></label>
    <input type="text" name="field1" 
           class="form-control form-control-lg 
               <?php echo (!empty($data['field1_err'])) ? 'is-invalid' : ''; ?>" 
            value="<?php echo $data['field1']; ?>" 
            placeholder="Add a field1...">
    <span class="invalid-feedback"><?php echo $data['field1_err']; ?></span>
</div>    
<div class="form-group">
    <label>Field2:<sup>*</sup></label>
    <textarea name="field2" class="form-control form-control-lg 
        <?php echo (!empty($data['field2_err'])) ? 'is-invalid' : ''; ?>" 
        placeholder="Add field2..."><?php echo $data['field2']; ?></textarea>
    <span class="invalid-feedback"><?php echo $data['field2_err']; ?></span>
</div>