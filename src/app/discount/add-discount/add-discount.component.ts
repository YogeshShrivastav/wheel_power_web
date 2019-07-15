import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-add-discount',
  templateUrl: './add-discount.component.html',
  animations: [slideToTop()]
})
export class AddDiscountComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
