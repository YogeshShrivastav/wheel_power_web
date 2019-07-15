import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { DatabaseService } from 'src/app/database.service';
import { DialogService } from 'src/app/dialog.service';

@Component({
  selector: 'app-stock-shift-detail',
  templateUrl: './stock-shift-detail.component.html',
})
export class StockShiftDetailComponent implements OnInit {

  item_id:any='';
  loader:any='';
  constructor(public route:ActivatedRoute,public db:DatabaseService,public router:Router,public dialog:DialogService) {
    this.route.params
    .subscribe(resp=>{
      this.item_id =resp['id'];
      console.log(this.item_id);
      
    })
   }

  ngOnInit() {
    this.get_item_detail();
  }

  item_detail:any=[];
  get_item_detail()
  {
    this.loader=1;
    this.db.getData(this.item_id,"stock/get_transfer_detail")
    .subscribe(resp=>{
      console.log(resp);
      this.item_detail = resp['item_detail'];
      for(var i=0; i<this.item_detail.length;i++)
      {
        this.item_detail[i]['accept_qty'] = '';
        this.item_detail[i]['reject_qty'] = '';
      }
      this.loader='';
    })
  }

  submit_record()
  {
    console.log(this.item_detail);
    this.db.getData(this.item_detail,"stock/transfer_action")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.dialog.success("Success","Stock Received");
        this.router.navigate(['stock-list']);
      }
    })
  }

  check_qty(indx)
  {
    console.log(this.item_detail[indx]['accept_qty']);
    console.log(this.item_detail[indx]['qty']);
    if(this.item_detail[indx]['accept_qty'] == '')
    {
      this.item_detail[indx]['accept_qty'] = 0;
    }
    if(parseInt(this.item_detail[indx]['accept_qty']) > parseInt(this.item_detail[indx]['qty']))
    {
      this.item_detail[indx]['accept_qty'] = this.item_detail[indx]['qty'];
      this.item_detail[indx]['reject_qty'] = 0;
    }
    else
    {
      this.item_detail[indx]['reject_qty'] = Math.abs(parseInt(this.item_detail[indx]['accept_qty']) - parseInt(this.item_detail[indx]['qty']));
    }
    
  }
}
