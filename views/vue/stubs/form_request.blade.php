class FormRequest {
toRequest() {
    const result: Record<string, any> = {};
const obj = this.makeFlattern()
    for (const [key, value] of Object.entries(obj)) {
    if (value !== undefined && value !== null && !Array.isArray(value) ) {
        result[key] = value;
    }
    if (value && Array.isArray(value) && value.length > 0) {
        let i=0;
        result[key] = []
        for (i = 0; i < value.length; i++){
            if (value) {
                if (value[i].hasOwnProperty('value') && value[i].hasOwnProperty('label') && value[i].hasOwnProperty('field')) {
                    result[value[i].field].push(value[i].value ?? value[i].id);
                }
                if (value[i].hasOwnProperty('id') && value[i].hasOwnProperty('name') && value[i].hasOwnProperty('value')) {
                    result[key].push(value[i].id ?? value[i].value);
                }

                // if (value[i].id){
                //   result[key].push(value[i].id)
                else {
                    result[key].push(value[i])
            }
            }
        }
      }
}
    return {'search': result};
  }

  toCreateRequest(){
    const result: Record<string, string|NonNullable<unknown>> = {};
    for (const [key, value] of Object.entries(this)) {
    if (key.endsWith('_id') && value){
        if (value.isInteger || typeof value === 'string'){
            result[key] = value
        } else {
            if (typeof value === 'object' && !Array.isArray(value) && value.hasOwnProperty('id')) {
                result[key] = value.id
          }
        }
      } else {
        result[key] = value
      }
}
    return  result;
  }

  toAdvanceRequest(){
    const result: Record<string, any> = {};
    for (const [key, value] of Object.entries(this)) {
    if (typeof value === 'object' && !Array.isArray(value)){
        for (const [k, v] of Object.entries(value)){
            result[key][k] = v
        }
      }
      if (value && Array.isArray(value) && value.length > 0) {
          let i=0;
        result[key] = []
        for (i = 0; i < value.length; i++){
            if (value[i].id){
                result[key].push(value[i].id)
          } else{
                result[key].push(value[i])
          }
        }
      }
    }
    return {'search': result};
  }

  makeFlattern(): Record<string, any> {
    return flattenObject(this)
  }

  recurse(current: Record<string, any>, parentKey: string | null) {
    const result: Record<string, unknown> = {};
    for (const key in current) {
    const value = current[key];
    const newKey = parentKey ? parentKey + '.' + key : key;

    if (typeof value === 'object') {
        this.recurse(value as Record<string, any>, newKey);
      } else {
        result[newKey] = value;
    }
    }
    return result;
  }

}


export {FormRequest}


function flattenObject(obj: Record<string, any>): Record<string, any> {
    const result: Record<string, unknown> = {};

  function recurse(current: Record<string, any>, parentKey: string | null) {
        for (const key in current) {
            const value = current[key];
            const newKey = key//parentKey ? parentKey + '.' + key : key;

      if (value && !Array.isArray(value) && typeof value === 'object') {
                if (value.hasOwnProperty('value') && value.hasOwnProperty('label') && value.hasOwnProperty('field'))
                {
                    return result[value.field] = value.value ?? value.id;
                }
                if (value.hasOwnProperty('id') && value.hasOwnProperty('name') && value.hasOwnProperty('value'))
                {
                    return result[newKey] = value.id ?? value.value;
                }
                else {
                    recurse(value as Record<string, unknown>, newKey);
        }
            } else {
                result[newKey] = value;
            }
    }
  }

  recurse(obj, null);

  return result;
}

