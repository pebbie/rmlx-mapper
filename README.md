# RMLX Mapper
RML processor with RMLx dialect. previously it's closely built with the RMLx web interface as a backend in [1]
the current repository contains the extracted processor and implemented in PHP 7, additionally refactored to make PSR-4 compliant

## RMLx : Difference with RML.io specs

 - rmlx:sourceTemplate : make source a template (for parameterization over the same model)
 - rmlx:defaultValue : introduce 'variables' available to use in reference/template from fixed value, command line parameters, web params
 - rmlx:predicateInv : make the triple relation inversed when emitted (o, p, s)
 - rmlx:transform : invoke built-in/external function to transform a value from a variable (e.g. reference, defaultValue)

## Data transformations : functions and API invocations

data transformation rule is defined using rmlx:transform. 

when rmlx:transform relation is assigned to a triple map node, it will be invoked once in case the triplemapping has no source or whenever each source is iterated (e.g. each CSV row) and associated (output) variables will be added/updated.

this feature is built upon the generalization and RDF-ization of rr:inverseExpression in R2RML

built-in functions (implemented in the paper version) : 
- [ ] rop:make_array : assign empty array to a variable
- [ ] rop:array_push : push value at the end of an array
- [ ] rop:trim : remove whitespace at the begininng and end of a string variable
- [ ] rop:replace : replace a string occurence in a string variable
- [ ] rop:split : split a string variable into an array variable
- [ ] rop:md5 : calculate MD5 hash of a string variable
- [ ] rop:sha1 : calculate SHA1 hash of a string variable
- [ ] rop:assign : assign a constant string value to a variable (behaves like rmlx:defaultValue)
- [ ] rop:auto_increment : get the current global counter
- [ ] rop:reset_auto_increment : reset global counter to 0

## References
[1] Aryan, Peb R., et al. "RMLx: Mapping interface for integrating open data with linked data exploration environment." 2017 1st International Conference on Informatics and Computational Sciences (ICICoS). IEEE, 2017. [code](https://bitbucket.org/ldlab/rmlx/src/master/) [OSF project page](https://osf.io/8yezw/)
