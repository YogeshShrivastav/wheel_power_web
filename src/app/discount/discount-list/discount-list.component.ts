import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-discount-list',
  templateUrl: './discount-list.component.html',
  animations: [slideToTop()]
})
export class DiscountListComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
