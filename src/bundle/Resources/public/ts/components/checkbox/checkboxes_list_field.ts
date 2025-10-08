import { BaseInputsList } from '../../partials';

export class CheckboxesListField extends BaseInputsList<string[]> {
    constructor(container: HTMLDivElement) {
        super(container);

        console.log(container);
    }

    init() {
        super.init();
    }
}
