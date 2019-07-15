import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';
import { ActivatedRoute } from '@angular/router';
import { MatDialog } from '@angular/material';
import { WarehouseDialogComponent } from 'src/app/warehouse-dialog/warehouse-dialog.component';
import { ToastrManager } from 'ng6-toastr-notifications';
import { OrderDispatchDialogComponent } from '../order-dispatch-dialog/order-dispatch-dialog.component';
import { DialogService } from 'src/app/dialog.service';

@Component({
  selector: 'app-order-detail',
  templateUrl: './order-detail.component.html',
})
export class OrderDetailComponent implements OnInit {
  
  formData=new FormData;
  order_id:any='';
  page_type:any='';
  params:any='';
  
  toggle:boolean= false;
  
  constructor(public db:DatabaseService,public route:ActivatedRoute,public dialog:MatDialog,public toastr:ToastrManager,public dialog_2:DialogService) {
    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.order_id = resp.id;
      this.page_type = resp.type;
      this.params = resp.para;
      console.log(this.params);
    });
    
  }
  loader:any='';
  ngOnInit() {
    console.log(this.order_id);
    if(this.params == 'full')
    {
      this.get_order_detail();     ///////////// Full detail
    }
    else
    {
      this.get_packing_data();      ///////// Packin Detail
    }
    
    this.get_order_detail();
    this.warehouse();
  }
  
  order_detail:any={};
  order_item:any=[];
  packing_item:any=[];
  packing:any={};
  packing_list:any={};
  order_payment:any=[];
  order_credit_detail:any={};
  user_detail:any={};
  dispatch_detail:any={};
  customer_detail:any={};
  tmp_arr:any=[];
  warehouse_list:any=[];
  data:any={};
  get_order_detail()
  {    
    this.loader = 1;
    this.db.getData({'order_id':this.order_id},"order/order_detail")
    .subscribe(resp=>{
      console.log(resp);
      this.order_detail = resp['order_detail'];
      this.order_item = resp['order_item'];
      for(var i=0;i<this.order_item.length; i++)
      {
        let new_ware_stock=0;
        let new_ware_id='';
        this.order_item[i]['total_amount'] = parseInt(this.order_item[i]['qty']) * parseInt(this.order_item[i]['price']);
        for(let j=0;j<this.order_item[i]['warehouse_stock'].length;j++)
        {
          if(this.order_item[i]['warehouse_stock'][j]['warehouse_name'] == "CHOPRA" || this.order_item[i]['warehouse_stock'][j]['warehouse_name'] == "BASMENT")
          {
            new_ware_stock = new_ware_stock+parseInt(this.order_item[i]['warehouse_stock'][j]['current_stock']);
            new_ware_id = this.order_item[i]['warehouse_stock'][j]['warehouse_id'];
          }
          else
          {
            this.tmp_arr.push(this.order_item[i]['warehouse_stock'][j]);
          }
        }
        if(new_ware_stock)
        {
          this.tmp_arr.push({'warehouse_id':new_ware_id,'warehouse_name':'BASMENT AND CHOPRA','current_stock':new_ware_stock});
        }
        this.order_item[i]['warehouse_stock']=this.tmp_arr;
        this.tmp_arr=[];
      }
      
      this.packing_list = resp['packing_list'];
      this.dispatch_detail = resp['dispatch'];
      this.order_payment = resp['order_payment'];
      this.customer_detail = resp['customer_detail'];
      this.order_credit_detail = resp['order_credit_detail'];
      this.user_detail = resp['user_detail'];
      this.calculate();
      this.loader='';
    })
  }
  
  get_packing_data()
  {
    this.db.getData(this.order_id,"order/order_packing_detail")
    .subscribe(resp=>{
      console.log(resp);
      this.packing = resp['packing'];
      this.packing_item = resp['packing_item'];
    });
  }
  
  assign:any=false;
  change_status()
  {
    console.log(this.order_detail.status);
    // this.order_detail.status = "assign";
    this.assign = true;
  }
  
  // get_order_dispatch_detail()
  // {    
  //   this.loader = 1;
  //   this.db.getData(this.order_id,"order/order_dispatch_detail")
  //   .subscribe(resp=>{
  //     console.log(resp);
  //     this.order_detail = resp['order_detail'];
  //     this.order_item = resp['order_item'];
  //     this.packing_item = resp['packing_item'];
  //     for(var i=0;i<this.order_item.length; i++)
  //     {
  //       this.order_item[i]['pack_qty'] = 0;
  //       this.order_item[i]['item'] = false;
  //     }
  //     this.packing_list = resp['packing_list'];
  //     this.order_payment = resp['order_payment'];
  //     this.customer_detail = resp['customer_detail'];
  //     this.order_credit_detail = resp['order_credit_detail'];
  //     this.user_detail = resp['user_detail'];
  //     this.loader='';
  //   })
  // }
  
  warehouse()
  {
    this.db.getData("","warehouse/stock_warehouse_list")
    .subscribe(resp=>{
      console.log(resp);
      this.warehouse_list = resp['warehouse_list'];
      let new_ware_id='';
      
      for(let i=0;i<this.warehouse_list.length;i++)
      {
        if(this.warehouse_list[i]['warehouse_name']== "CHOPRA" || this.warehouse_list[i]['warehouse_name'] == "BASMENT")
        {
          new_ware_id = this.warehouse_list[i]['id'];
        }
        else
        {
          this.tmp_arr.push(this.warehouse_list[i]);
        }
      }
      if(new_ware_id)
      {
        this.tmp_arr.push({'id':new_ware_id,'warehouse_name':'BASMENT AND CHOPRA'});
      }
      this.warehouse_list=this.tmp_arr;
      this.tmp_arr=[];
      console.log(this.warehouse_list);
      
    })
  }
  
  varify:any={};
  varify_payment(id,action)
  {
    this.loader="1";
    console.log(id);
    this.varify.id=id;
    this.varify.action=action;
    this.db.getData(this.varify,"order/varify_payment")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.get_order_detail();
      }
    });
  }
  
  open_wharehouse_dialog(p_id)
  {
    const dialogRef  = this.dialog.open(WarehouseDialogComponent,{
      data:{
        product_id:p_id,
      },
    });
    
    dialogRef.afterClosed().subscribe(result=>{
      console.log(result);
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
  
  update_qty(indx)
  {
    if(this.order_item[indx]['qty'] == null)
    { this.order_item[indx]['qty'] = 0; }
    if(this.order_item[indx]['price'] == null)
    { this.order_item[indx]['price'] = 0; }
    this.calculate();
    console.log(this.total_amount);
    
    this.db.getData({'order':this.order_item[indx],'order_total':this.total_amount},"order/update_order")
    .subscribe(resp=>{
      console.log(resp);
    })
  }
  
  update_amount(indx)
  {
    this.total_amount=0;
    for(var i=0;i<this.order_item.length;i++)
    {
      if(this.order_item[i]['total_amount'] == null)
      { this.order_item[i]['total_amount'] = 0; }
      this.total_amount = this.total_amount+parseInt(this.order_item[i]['total_amount']);
    }
    
    this.db.getData({'order_id':this.order_item[indx]['order_id'],'order_total':this.total_amount},"order/update_order_total")
    .subscribe(resp=>{
      console.log(resp);
    })
  }
  
  total_qty:any=0;
  total_rate:any=0;
  total_amount:any=0;
  calculate()
  {
    this.total_qty=0;
    this.total_rate=0;
    this.total_amount=0;
    for(var i=0;i<this.order_item.length;i++)
    {           
      this.order_item[i]['total_amount'] = parseInt(this.order_item[i]['qty']) * parseInt(this.order_item[i]['price']);
      this.total_qty = this.total_qty+parseInt(this.order_item[i]['qty']);
      this.total_rate = this.total_rate+parseInt(this.order_item[i]['price']);
      this.total_amount = this.total_amount+parseInt(this.order_item[i]['total_amount']);
    }
  }
  
  
  warehouse_name:any='';
  assign_warehouse(data)
  {
    this.order_detail.status = "assign";

    console.log(data);
    this.db.getData(data,"order/assign_order")
    .subscribe(resp=>{
      console.log(resp);
      this.toastr.successToastr("Order Assigned");
      this.get_order_detail();
    })
  }
  
  check_payment()
  {
    if(this.order_detail.payment_varification == 'not varified')
    {
      this.toastr.warningToastr('Varify the Payment First');
      this.order_detail.status = 'pending';
    }
    console.log(this.order_detail);
  }
  
  generate_invoice()
  {
    console.log(this.data);
    this.data.packing_id = this.packing.packing_id;
    // this.data.order_id = this.order_id;
    if(this.selectedFile.length > 0)
    {
      this.formData.append('image',this.selectedFile[0],this.selectedFile.name);
    }
    for(var property in this.data)
    {
      this.formData.append(property,JSON.stringify(this.data[property]));
    }
    
    this.db.uploadImage(this.formData,"order/generate_invoice")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.dialog_2.success("Success","Invoice Generated");
        this.get_packing_data();
      }
    });
  }
  
  upload_file:any='';
  selectedFile:any=[];
  selectFile(event)
  {
    console.log(this.data);
    this.selectedFile.push(event.target.files[0]);
    console.log(this.selectedFile);
    this.upload_file = this.selectedFile[0].name;
  }
  
  download_invoice(doc_name)
  {
    window.open(this.db.download_url+doc_name);
  }
  
  dispatch_order()
  {
    // this.db.getData("")
  }
}
