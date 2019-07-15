import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { ActivatedRoute } from '@angular/router';

@Component({
  selector: 'app-outgoing-stock',
  templateUrl: './outgoing-stock.component.html',
  animations: [slideToTop()]
})
export class OutgoingStockComponent implements OnInit {

  stock_id:any=0;
  constructor(public route:ActivatedRoute) {
    this.route.params.subscribe(resp=>{
      this.stock_id=resp["id"];
      console.log(resp);      
    })
   }

  ngOnInit() {
  }

}
