import Checkbox from './Checkbox';

export default class ThreeStateCheckbox extends Checkbox {
    init() {
        super.init();

        console.log(this._inputElement);
    }
}
