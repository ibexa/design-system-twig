import { HTMLElementIDSInstance } from '../shared/types';

const setInstance = <InstanceType>(domElement: HTMLElementIDSInstance<InstanceType>, instance: InstanceType): void => {
    if (domElement.idsInstance) {
        throw new Error('Instance for this DOM element already exists!');
    }

    domElement.idsInstance = instance; // eslint-disable-line no-param-reassign
};
const hasInstance = <InstanceType>(domElement: HTMLElementIDSInstance<InstanceType>): boolean => {
    return !!domElement.idsInstance;
};
const getInstance = <InstanceType>(domElement: HTMLElementIDSInstance<InstanceType>): InstanceType => {
    if (domElement.idsInstance) {
        return domElement.idsInstance;
    }

    throw new Error('Instance for this DOM element doesn\'t exists!');
};
const clearInstance = <InstanceType>(domElement: HTMLElementIDSInstance<InstanceType>): void => {
    delete domElement.idsInstance; // eslint-disable-line no-param-reassign
};

export { setInstance, getInstance, clearInstance, hasInstance };
