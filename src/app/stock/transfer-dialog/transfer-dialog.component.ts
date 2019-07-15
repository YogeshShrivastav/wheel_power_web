import { Component, OnInit, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialog } from '@angular/material';
import { DatabaseService } from 'src/app/database.service';
import { DialogService } from 'src/app/dialog.service';

@Component({
  selector: 'app-transfer-dialog',
  templateUrl: './transfer-dialog.component.html',
})
export class TransferDialogComponent implements OnInit {

  loader:any='';
  param:any={};
  row:any={};
  val:any={};
  page_type:any='';
  constructor(@Inject(MAT_DIALOG_DATA) public data,public db:DatabaseService,public dialog:MatDialog) {
    this.param = data;
    this.row = this.param.row;
    this.page_type = this.param.page_type;
    this.row.page_type = this.page_type;
   }

  ngOnInit() {
    this.stock_transfer_detail();
  }

  transfer_detail_list:any=[];
  stock_transfer_detail()
  {
    this.loader=1;
    this.db.getData(this.row,"stock/stock_transfer_detail")
    .subscribe(resp=>{
      console.log(resp);
      this.transfer_detail_list = resp['transfer_detail'];
      this.loader="";
    })
  }

  close_dialog()
  {
    this.dialog.closeAll();
  }
}
