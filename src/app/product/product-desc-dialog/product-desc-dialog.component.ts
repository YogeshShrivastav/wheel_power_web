import { Component, OnInit, Inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MAT_DIALOG_DATA, MatDialog } from '@angular/material';
import { DatabaseService } from 'src/app/database.service';
import { ToastrManager } from 'ng6-toastr-notifications';

@Component({
  selector: 'app-product-desc-dialog',
  templateUrl: './product-desc-dialog.component.html',
})
export class ProductDescDialogComponent implements OnInit {

  constructor(public route:ActivatedRoute,@Inject(MAT_DIALOG_DATA) public data: any, private dialogRef: MatDialog,public db:DatabaseService,public toastr:ToastrManager) { }

  ngOnInit() {
  }

  update:any={};
  submitproduct()
  {
    this.update.product_id=this.data.product_id;
    this.update.attribute_name=this.data.product_type;
    this.update.value=this.data.product_val;
    console.log(this.update);
    this.db.getData(this.update,"products/update_attribute")
    .subscribe(resp=>{
      console.log(resp);
      if(resp)
      {
        this.toastr.successToastr('Updated','Success!')
      }
      
    })
    this.dialogRef.closeAll();
  }
  onNoClick()
  {
    this.dialogRef.closeAll();
  }

}
