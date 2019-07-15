import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-gift-list',
  templateUrl: './gift-list.component.html',
  animations: [slideToTop()]

})
export class GiftListComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
