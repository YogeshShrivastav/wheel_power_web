import { Component, OnInit, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialog } from '@angular/material';
import { DatabaseService } from 'src/app/database.service';

@Component({
  selector: 'app-return-dialog',
  templateUrl: './return-dialog.component.html',
})
export class ReturnDialogComponent implements OnInit {

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
    this.stock_return_detail();
  }

  return_detail_list:any=[];
  stock_return_detail()
  {
    this.loader=1;
    this.db.getData(this.row,"stock/stock_return_detail")
    .subscribe(resp=>{
      console.log(resp);
      this.return_detail_list = resp['return_detail'];
      this.loader='';
    })
  }

  close_dialog()
  {
    this.dialog.closeAll();
  }
}
