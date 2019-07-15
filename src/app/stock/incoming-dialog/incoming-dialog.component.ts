import { Component, OnInit, Inject } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { MAT_DIALOG_DATA, MatDialog } from '@angular/material';
import { DatabaseService } from 'src/app/database.service';
import { SessionService } from 'src/app/session.service';

@Component({
  selector: 'app-incoming-dialog',
  templateUrl: './incoming-dialog.component.html',
})
export class IncomingDialogComponent implements OnInit {

  stock_data:any={};
  val:any={};
  loader:any='';
  constructor(public route:ActivatedRoute,@Inject(MAT_DIALOG_DATA) public data,public dialog:MatDialog,public db:DatabaseService,public session:SessionService) { 
    this.stock_data = data.row;
    console.log(this.stock_data);
    
    this.val.product_id = this.stock_data.product_id;
    this.val.vendor_id = this.stock_data.vendor_id;
  }
  session_data:any={};
  abq_user:any={};
  ngOnInit() {
   this.session_data=this.session.GetSession();
   this.abq_user=this.session_data.value;
   this.val.warehouse_id = this.abq_user.id;
   this.incoming_detai()
  }

  data_list:any=[];
  incoming_detai()
  {
    this.loader=1;
    this.db.getData(this.val,"stock/incoming_stock_detail")
    .subscribe(resp=>{
      console.log(resp);
      this.data_list = resp['incoming_detail'];
      this.loader="";
    })
  }

  close_dialog()
  {
    this.dialog.closeAll();
  }
}
