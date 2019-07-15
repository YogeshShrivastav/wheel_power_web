import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';
import { ActivatedRoute, Router } from '@angular/router';
import { MatDialog } from '@angular/material';
import { ToastrManager } from 'ng6-toastr-notifications';
import { WarehouseDialogComponent } from 'src/app/warehouse-dialog/warehouse-dialog.component';
import { SessionService } from 'src/app/session.service';
import { DialogService } from 'src/app/dialog.service';
import { OrderDispatchDialogComponent } from '../order-dispatch-dialog/order-dispatch-dialog.component';

@Component({
  selector: 'app-order-dispatch-detail',
  templateUrl: './order-dispatch-detail.component.html',
})
export class OrderDispatchDetailComponent implements OnInit {

  order_id:any='';
  data:any={};
  session_data:any={};
  abq_user:any={};
  page_type:any='';
  constructor(public db:DatabaseService,public route:ActivatedRoute,public dialog:MatDialog,public toastr:ToastrManager,public session:SessionService,public dialog2:DialogService,public router:Router) {
    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.page_type = resp.type;
      this.order_id = resp.id;
    });

    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user.id);
   }

  item:any='';
  loader:any=1;
  ngOnInit() {
    if(this.page_type != "packing_pend")
    {
      this.dispatch_pend_data();
    }
    else
    {
      this.get_order_dispatch_detail();
    }
  }

  order_detail:any={};
  order_item:any=[];
  packing_item:any=[];
  packing_list:any=[];
  order_payment:any=[];
  order_credit_detail:any={};
  user_detail:any={};
  dispatch_detail:any={};
  customer_detail:any={};
  tmp_arr:any=[];
  wareshouse_list:any=[];
  dispatch:any=true;
  total_amount:any=0;
  get_order_dispatch_detail()
  {    
    this.loader = '1';
    this.db.getData({'order_id':this.order_id,'page_type':this.page_type,'warehouse_id':this.abq_user.id},"order/order_dispatch_detail")
    .subscribe(resp=>{
    console.log(resp);
    this.order_detail = resp['order_detail'];
    this.order_item = resp['order_item'];
    for(var i=0;i<this.order_item.length; i++)
    {
      this.order_item[i]['pack_qty'] = 0;
      this.order_item[i]['item'] = false;
      for(var j=0;j<this.packing_arr.length;j++)
      {
        if(this.order_item[i]['cat_no'] == this.packing_arr[j]['cat_no'])
        {
          this.order_item[i] = this.packing_arr[j];
        }
      }
    }
    this.packing_item = resp['packing_item'];
    this.packing_list = resp['packing'];
    this.dispatch_detail = resp['dispatch'];
    this.order_payment = resp['order_payment'];
    this.customer_detail = resp['customer_detail'];
    this.order_credit_detail = resp['order_credit_detail'];
    this.user_detail = resp['user_detail'];
    this.loader='';
    });
  }


  dispatch_pend_data()
  {    
    this.loader = '1';
    this.db.getData({'packing_id':this.order_id,'page_type':this.page_type,'warehouse_id':this.abq_user.id},"order/order_dispatch_pend")
    .subscribe(resp=>{
    console.log(resp);
    this.order_detail = resp['order_detail'];
    this.order_item = resp['order_item'];
    this.packing_item = resp['packing_item'];
    this.packing_list = resp['packing'];
    this.customer_detail = resp['customer_detail'];
    this.loader='';
    });
  }


  open_dialog(p_id)
  {
    const dialogRef  = this.dialog.open(OrderDispatchDialogComponent,{
      data:{
        packing_id:p_id,
      },
    });

    dialogRef.afterClosed().subscribe(result=>{
      console.log(result);
    });
  }

  
  print(): void {
    let printContents, popupWin;
    printContents = document.getElementById('print-section').innerHTML;
    popupWin = window.open('', '_blank', 'top=0,left=0,height=100%,width=auto');
    popupWin.document.open();
    popupWin.document.write(`
    <html>
    <head>
    <title>Print tab</title>
    
    <style>

    body
    {
      font-family: 'arial';
    }
    </style>
    
    </head>
    
    <body onload="window.print();window.close()">${printContents}</body>
    </html>`
    );
    popupWin.document.close();
  }

  // warehouse_name:any='';
  // assign_warehouse(data)
  // {
  //   console.log(data);
  //   this.db.getData(data,"order/assign_order")
  //   .subscribe(resp=>{
  //     console.log(resp);
  //     // this.get_order_detail();
  //     this.get_order_dispatch_detail();
  //     this.warehouse_name = resp['warehouse_name'];
  //   })
  // }

  // check_payment()
  // {
  //   if(this.order_detail.payment_varification == 'not varified')
  //   {
  //     this.toastr.warningToastr('Varify the Payment First');
  //     this.order_detail.status = 'pending';
  //   }
  //   console.log(this.order_detail);
  // }

  check_qty(indx)
  {
    if(this.order_item[indx]['pack_qty'] > this.order_item[indx]['qty'] || this.order_item[indx]['pack_qty'] < 0)
    {
      this.toastr.warningToastr("Invalid QTY");
      this.order_item[indx]['pack_qty'] = this.order_item[indx]['qty'];
    }
    this.order_item[indx]['item']=true;
    if(this.order_item[indx].pack_qty == 0 || this.order_item[indx].pack_qty == null)
    {
      this.toastr.errorToastr("Packed QTY is Empty");
      // if(this.page_type == 'packing_pend')
      // {
      //   this.get_order_detail();
      // }
      // else
      // {
      // }
      this.get_order_dispatch_detail();
      // this.get_order_detail();
    }
    else
    {
      if(this.packing_arr.length == 0 )
      {     
        this.packing_arr.push(this.order_item[indx]);   
      }
      else
      {
        for(var i=0; i<this.packing_arr.length; i++) 
        {
          if(this.packing_arr[i].cat_no == this.order_item[indx].cat_no) 
          {
            this.packing_arr[i].pack_qty = parseInt(this.order_item[indx].pack_qty);
            break;
          }
          else if(i == this.packing_arr.length -1) 
          {
            this.packing_arr.push(this.order_item[indx]);
            break; 
          }
        } 
      }
    }
    console.log(this.packing_arr);
  }

  packing_arr:any=[];
  select_item(row,args)
  {
    console.log(row);
    if(args == true)
    {
      if(row.pack_qty == 0)
      {
        this.toastr.errorToastr("Packed QTY is Empty");
      this.get_order_dispatch_detail();
      // this.get_order_detail();
      }
      else
      {
        this.packing_arr.push(row);
      }
    }
    else
    {
      let index = this.packing_arr.findIndex( record => record.cat_no === row.cat_no );
      console.log(index);
      this.packing_arr.splice(index,1);
    }
    console.log(this.packing_arr);    
  }

  generate_packing()
  {
    this.data.order_id = this.order_id;
    this.data.warehouse_id = this.abq_user.id;
    this.data.customer_id = this.customer_detail.id;
    this.data.packing_item = this.packing_arr;
    this.db.getData(this.data,"order/order_packing")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
      this.dialog2.success("Success","Packing Generated");
      this.order_item = [];
      this.packing_arr = [];
      this.get_order_dispatch_detail();
      this.router.navigate(['/order-dispatch-detail/'+this.order_id+'/'+this.page_type]);
      }
    })
  }

  download_invoice(doc_name)
  {
    window.open(this.db.download_url+doc_name);
  }

  order_dispatch()
  {
    this.dialog2.confirm("Order")
    .then(resp=>{
      if(resp)
      {
        this.db.getData(this.order_id,"order/order_dispatch")
        .subscribe(resp=>{
          console.log(resp);
          if(resp == 'success')
          {
            this.dialog2.success("Success","Order successfully Dispatched");
            this.router.navigate(['/order-dispatch/dispatch']);
          }
       })
      }
    });       


    
  }
}
