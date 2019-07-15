import { Component, OnInit, Inject } from '@angular/core';
import { MatDialog, MAT_DIALOG_DATA } from '@angular/material';
import { ActivatedRoute } from '@angular/router';
import { DatabaseService } from 'src/app/database.service';

@Component({
  selector: 'app-order-payment-dialog',
  templateUrl: './order-payment-dialog.component.html',
})
export class OrderPaymentDialogComponent implements OnInit {

  order_id:any='';
  constructor(public dialog:MatDialog,public route:ActivatedRoute,@Inject(MAT_DIALOG_DATA) public data,public db:DatabaseService) {
    console.log(data);
    this.order_id = data.order_id;
   }

  ngOnInit() {
    this.get_payments();
  }
  loader:any='';
  payment_list:any=[];
  get_payments()
  {
    this.loader="1";
    this.db.getData(this.order_id,"order/get_payments")
    .subscribe(resp=>{
      console.log(resp);
      this.payment_list = resp['payment_list'];
      this.loader='';
    })
  }
  close_dialog()
  {
    this.dialog.closeAll();
  }

  varify_payment(id)
  {
    this.loader="1";
    console.log(id);
    this.db.getData(id,"order/varify_payment")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.get_payments();
      }
    })
  }
}
