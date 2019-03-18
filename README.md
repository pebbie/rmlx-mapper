# RMLX Mapper
RML (RDF Mapping Language) processor with RMLx dialect. 

## History
previously it's closely built with the RMLx web interface as a backend in [1]. The old mapping strategy was not so efficient esp. for larger files since the input and output graph is held in memory and the mapping strategy traverse the RML graph every time. It's OK for small-medium datasets though. But it's soon find some bottleneck when mapping 11MB of CSV file into RDF Data Cube with 4 measures requires more than 2GB of memory and took almost half an hour to do so.

Therefore, I decided to refactor the processor to make it more efficient. The current repository contains the extracted processor, reimplemented in PHP 7 OOP (previously was a collection of functions) and make it PSR-4 compliant. For the same case mentioned in the previous paragraph, the current implementation only requires 20MB and couple of minutes. 

The current strategy do the mapping in two steps: 
- parse the RML specification and build the mapping execution component tree (mapper)
- run the mapper

## Installation
`composer require pebbie/rmlx-mapper`

currently, in order to import this, your minimum stability must be `dev`

## Usage
`composer run rmlx <RML file>.ttl [key=value]*`

for example: `composer run rmlx data/test_const.rml.ttl` (see also other `test_*.rml.ttl` files)

the default output is to the terminal and using ntriples format. if you want to generate using other formats (e.g. `n3`), make sure to suppress the terminal output by setting `quiet=on` and then use `output_file=somefile.ttl output_format=n3` as additional argument.

### Issues
R2RML compatibility (relational DB) implementation is not a priority. The PDOSource is there as placeholder without working implementation.

## Under the hood

### RMLx : Difference with RML.io specs

RMLx was developed not long after the inception of RML (LDOW paper). So, some aspect of the current spec may not be implemented and some other features are implemented out of necessities for our publications/use-cases (statistical data, streaming sensors, Conference PC members, linked widgets mashup integrations ).

 - [x] `rmlx:sourceTemplate` : make source a template (for parameterization over the same model)
 - [x] `rmlx:defaultValue` : introduce 'variables' available to use in reference/template from fixed value, command line parameters, web params
 - [x] `rmlx:predicateInv` : make the triple relation inversed when emitted (o, p, s)
 - [ ] `rmlx:transform` : invoke built-in/external function to transform a value from a variable (e.g. reference, defaultValue)

### Data transformations : functions and API invocations

data transformation rule is defined using `rmlx:transform`. 

when `rmlx:transform` relation is assigned to a triple map node, it will be invoked once in case the triplemapping has no source or whenever each source is iterated (e.g. each CSV row) and associated (output) variables will be added/updated.

this feature is built upon the generalization and RDF-ization of `rr:inverseExpression` in R2RML

built-in functions (implemented in the paper version) : 
- [ ] `rop:make_array` : assign empty array to a variable
- [ ] `rop:array_push` : push value at the end of an array
- [ ] `rop:trim` : remove whitespace at the begininng and end of a string variable
- [ ] `rop:replace` : replace a string occurence in a string variable
- [ ] `rop:split` : split a string variable into an array variable
- [ ] `rop:md5` : calculate MD5 hash of a string variable
- [ ] `rop:sha1` : calculate SHA1 hash of a string variable
- [ ] `rop:assign` : assign a constant string value to a variable (behaves like rmlx:defaultValue)
- [ ] `rop:auto_increment` : get the current global counter
- [ ] `rop:reset_auto_increment` : reset global counter to 0

## References
[1] Aryan, Peb R., et al. "RMLx: Mapping interface for integrating open data with linked data exploration environment." 2017 1st International Conference on Informatics and Computational Sciences (ICICoS). IEEE, 2017. [code](https://bitbucket.org/ldlab/rmlx/src/master/) [OSF project page](https://osf.io/8yezw/)
