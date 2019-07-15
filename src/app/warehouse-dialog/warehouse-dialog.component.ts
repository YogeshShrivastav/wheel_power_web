import { Component, OnInit, Inject } from '@angular/core';
import { DatabaseService } from '../database.service';
import { ActivatedRoute } from '@angular/router';
import { MAT_DIALOG_DATA, MatDialog } from '@angular/material';

@Component({
  selector: 'app-warehouse-dialog',
  templateUrl: './warehouse-dialog.component.html',
})
export class WarehouseDialogComponent implements OnInit {

  product_id:any=0;
  total_stock:any=0;
  loader:any='';
  constructor(public db:DatabaseService,public route:ActivatedRoute,@Inject(MAT_DIALOG_DATA) public data,public dialogRef:MatDialog) {
    this.product_id = data.product_id;
    this.total_stock = data.total_stock;
    console.log(this.product_id);
    
   }

  ngOnInit() {
    this.warehouse_vise_data();
  }

  wh_prod_list:any=[];
  warehouse_vise_data()
  {
    this.loader="1";
    this.db.getData(this.product_id,"warehouse/get_warehouse_vise")
    .subscribe(resp=>{
      console.log(resp);
      this.wh_prod_list = resp['warehouse_product'];
      this.loader = '';
    });    
  }

  close_dialog()
  {
    this.dialogRef.closeAll();
  }
}
