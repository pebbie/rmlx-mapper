# Data file description


- test_const.rml.ttl : use only constant in each term (rr:subject, rr:predicate, rr:object) : expect 1 triple
- test_const_multiobj.rml.ttl : same as test_const.rml.ttl but with extra object : expect 2 triples
- test_iriobj.rml.ttl : use blank node as subject, assign with a class, make triple with IRI object : expect 2 triples
- test_subjmap_restconst.rml.ttl : usr rr:const in subjectmap and assign to a class : expect 1 triple