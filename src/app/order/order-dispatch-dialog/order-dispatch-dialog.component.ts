import { Component, OnInit, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialog } from '@angular/material';
import { DatabaseService } from 'src/app/database.service';

@Component({
  selector: 'app-order-dispatch-dialog',
  templateUrl: './order-dispatch-dialog.component.html',
})
export class OrderDispatchDialogComponent implements OnInit {

  loader:any='';
  param:any={};
  packing_id:any={};
  val:any={};
  page_type:any='';
  constructor(@Inject(MAT_DIALOG_DATA) public data,public db:DatabaseService,public dialog:MatDialog) {
    this.param = data;
    this.packing_id = this.param.packing_id;
    console.log(this.packing_id);
   }
  ngOnInit() {
    this.get_packing_item();
  }

  item_list:any=[];
  get_packing_item()
  {
    this.db.getData(this.packing_id,"order/get_packing_item")
    .subscribe(resp=>{
      console.log(resp);
      this.item_list = resp['item_list'];
    })
  }

  close_dialog()
  {
    this.dialog.closeAll();
  }

}
