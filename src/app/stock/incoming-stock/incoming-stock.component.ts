import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { ActivatedRoute } from '@angular/router';
import { DatabaseService } from 'src/app/database.service';
import { SessionService } from 'src/app/session.service';
import { MatDialog } from '@angular/material';
import { IncomingDialogComponent } from '../incoming-dialog/incoming-dialog.component';
import { TransferDialogComponent } from '../transfer-dialog/transfer-dialog.component';
import { ReturnDialogComponent } from '../return-dialog/return-dialog.component';

@Component({
  selector: 'app-incoming-stock',
  templateUrl: './incoming-stock.component.html',
  animations: [slideToTop()]
})
export class IncomingStockComponent implements OnInit {

   stock:any={};
   enct_cat:any='';
  constructor(public route:ActivatedRoute,public db:DatabaseService,public session:SessionService,public dialog:MatDialog) {
    this.route.params.subscribe(resp=>{
      this.enct_cat=resp["id"];
      this.stock.enct_cat = this.enct_cat;
      console.log(resp);      
    });
   }
   url:any='';
   loader:any='';
   session_data:any={};
   abq_user:any={};
   ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    
    console.log(this.abq_user);

    this.stock.warehouse_id = this.abq_user.id;
    this.url = this.db.download_url;
    this.get_incoming_stock();
    this.stock.search_type = "in";
  }

  stock_data_list:any=[];
  total_row:any=0;
  get_incoming_stock()
  {
    this.loader = "1";
    this.db.getData(this.stock,"stock/get_incoming_stock")
    .subscribe(resp=>{
      console.log(resp);
      this.stock_data_list=resp["incoming_list"];
      this.loader = "";
    });
  }

  get_transfer_stock()
  {
    console.log(this.stock);
    this.loader = "1";
    this.db.getData(this.stock,"stock/get_transfer_stock")
    .subscribe(resp=>{
      console.log(resp);
      this.stock_data_list=resp["stock_transfer"];
      this.loader = "";
    });
  }

  get_return_stock()
  {
    console.log(this.stock);
    this.loader = "1";
    this.db.getData(this.stock,"stock/get_return_stock")
    .subscribe(resp=>{
      console.log(resp);
      this.stock_data_list=resp["return_list"];
      this.loader = "";
    });
  }

  open_dialog(data)
  {
    const diaRef = this.dialog.open(IncomingDialogComponent,{
      data:{row: data}
    });

    diaRef.afterClosed().subscribe(resp=>{
      console.log(resp);
      
    })
  }

  open_transfer_dialog(data)
  {
    const diaref2 = this.dialog.open(TransferDialogComponent,{
      data:{
        row:data,
        page_type:this.stock.search_type
      }
    });

    diaref2.afterClosed().subscribe(resp=>{
      console.log(resp);
    })
  }

  open_return_dialog(data)
  {
    const diaref3 = this.dialog.open(ReturnDialogComponent ,{
      data:{
        row:data,
      }
    });

    diaref3.afterClosed().subscribe(resp=>{
      console.log(resp);
    })
  }

  incoming:any=1;
  transfer:any=0;
  dispatch:any=0;
  return:any=0;
  active(val)
  {
    if(val == 'incoming')
    {
      this.incoming = 1;
      this.transfer = 0;
      this.dispatch = 0;
      this.return = 0;
    }
    if(val == 'transfer')
    {
      this.incoming = 0;
      this.transfer = 1;
      this.dispatch = 0;
      this.return = 0;
    }
    if(val == 'dispatch')
    {
      this.incoming = 0;
      this.transfer = 0;
      this.dispatch = 1;
      this.return = 0;
    }
    if(val == 'return')
    {
      this.incoming = 0;
      this.transfer = 0;
      this.dispatch = 0;
      this.return = 1;
    }
  }

  in:any=1;
  out:any=0;
  active_tra(arg)
  {
    if(arg == "in")
    {
      this.stock.search_type = "in";
      this.in = 1;
      this.out = 0;
    }
    if(arg == 'out')
    {
      this.stock.search_type = "out";
      this.in = 0;
      this.out =1;
    }
  }
}
