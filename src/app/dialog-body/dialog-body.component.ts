import { Component, OnInit,Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef, MatDialog } from '@angular/material';
import { ActivatedRoute, Router } from '@angular/router';
import { DatabaseService } from '../database.service';
import { DialogService } from '../dialog.service';
import { ToastrManager } from 'ng6-toastr-notifications';

@Component({
  selector: 'app-dialog-body',
  templateUrl: './dialog-body.component.html',
})
export class DialogBodyComponent implements OnInit {

  type:boolean=false;
  list_data:any=[];
  subcategory_list:any=[];
  brand_list:any=[];
  constructor(public route:ActivatedRoute,@Inject(MAT_DIALOG_DATA) public data: any, private dialogRef: MatDialog,public db:DatabaseService,public dialog:DialogService,public router:Router,public toastr: ToastrManager) { 
  console.log(data);
  
    if(data.product_type == 'category')
    {
      this.type=true;
        this.db.getData("","products/get_product_category_indp")
        .subscribe(resp=>{
          console.log(resp);
          this.list_data=resp
      });   
    }
    else if(data.product_type == 'sub_category')
    {
      this.type=true;
      console.log("yes");
      this.db.getData("","products/get_product_sub_category_indp")
      .subscribe(resp=>{
          console.log(resp);
          this.list_data=resp
      });
    }
    else if(data.product_type == 'brand')
    {
      this.type=true;
      this.db.getData("","products/get_product_brand")
      .subscribe(resp=>{
          console.log(resp);
          this.list_data=resp
      });
    }
    
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

  ngOnInit() {
  }

  MobileNumber(event: any) {
    const pattern = /[0-9\+\-\ ]/;
    let inputChar = String.fromCharCode(event.charCode);
    if (event.keyCode != 8 && !pattern.test(inputChar)) {
      event.preventDefault();
    }
  }

}
