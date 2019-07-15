import { Component, OnInit } from '@angular/core';
import { DatabaseService } from 'src/app/database.service';
import { ActivatedRoute } from '@angular/router';
import { MatDialog } from '@angular/material';
import { CustomerEditDialogComponent } from '../customer-edit-dialog/customer-edit-dialog.component';

@Component({
  selector: 'app-customer-detail',
  templateUrl: './customer-detail.component.html',
})
export class CustomerDetailComponent implements OnInit {

  customer_id:any='';
  constructor(public db:DatabaseService,public route:ActivatedRoute,public dialog:MatDialog) {
    this.route.params.subscribe(resp=>{
      console.log(resp);
      this.customer_id = resp['id'];
      console.log(this.customer_id);
    })
   }
  ngOnInit() {
    this.get_customer_detail();
  }

  loader:any='';
  customer:any={};
  order_list:any=[];
  get_customer_detail()
  {
    this.loader = "1";
    this.db.getData(this.customer_id,"customer/get_customer_detail")
    .subscribe(resp=>{
      console.log(resp);
      this.customer = resp['customer'];
      this.order_list = resp['order_list'];
      this.loader="";
    })
  }

  open_edit(edit_item)
  {
    this.customer.edit_item=edit_item;
    const dialog1 = this.dialog.open(CustomerEditDialogComponent,{data:this.customer});
    dialog1.afterClosed().subscribe(result => {
      console.log(`Dialog result: ${result}`);
    });
  }
}
