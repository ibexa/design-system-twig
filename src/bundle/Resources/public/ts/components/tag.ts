import { Base } from '../partials';

export default class Tag extends Base {
    constructor(container: HTMLDivElement) {
        super(container);
        const tagElement = this._container.querySelector<HTMLElement>('.ids-tag');

        if (!tagElement) {
            throw new Error('No tag element found for this container!');
        }
    }
}
