import { HTMLElementIbexaInstance } from '../shared/types';

const setInstance = <InstanceType>(domElement: HTMLElementIbexaInstance<InstanceType>, instance: InstanceType): void => {
    if (domElement.ibexaInstance) {
        throw new Error('Instance for this DOM element already exists!');
    }

    domElement.ibexaInstance = instance; // eslint-disable-line no-param-reassign
};
const hasInstance = <InstanceType>(domElement: HTMLElementIbexaInstance<InstanceType>): boolean => {
    return !!domElement.ibexaInstance;
};
const getInstance = <InstanceType>(domElement: HTMLElementIbexaInstance<InstanceType>): InstanceType => {
    if (domElement.ibexaInstance) {
        return domElement.ibexaInstance;
    }

    throw new Error('Instance for this DOM element doesn\'t exists!');
};
const clearInstance = <InstanceType>(domElement: HTMLElementIbexaInstance<InstanceType>): void => {
    delete domElement.ibexaInstance; // eslint-disable-line no-param-reassign
};

export { setInstance, getInstance, clearInstance, hasInstance };
