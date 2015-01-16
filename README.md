# build-tools-lib

This libraries provides features for [build-tools](https://github.com/javihgil/build-tools) package.

## Usage in other projects

**Include as git submodule**

```bash
    $ git submodule add git@github.com:javihgil/build-tools-libs.git lib
```
    
**Usage in phing**

```xml
    <!-- types -->
    <typedef name="modules" classname="lib.Phing.types.Modules" />
    <typedef name="module"  classname="lib.Phing.types.Module" />

    <!-- tasks -->
    <taskdef name="call"    classname="lib.Phing.tasks.CallTask" />
    <taskdef name="ccopy"   classname="lib.Phing.tasks.CodeCopyTask" />
    <taskdef name="composr" classname="lib.Phing.tasks.ComposrTask" />
    <taskdef name="dev"     classname="lib.Phing.tasks.DevTask" />
    <taskdef name="info"    classname="lib.Phing.tasks.InfoTask" />
    <taskdef name="modulei" classname="lib.Phing.tasks.ModuleIteratorTask" />
    <taskdef name="package" classname="lib.Phing.tasks.PackageTask" />
    <taskdef name="rm"      classname="lib.Phing.tasks.RmTask" />
    <taskdef name="repo"    classname="lib.Phing.tasks.RepositoryTask" />
    <taskdef name="symfony" classname="lib.Phing.tasks.SymfonyTask" />
    <taskdef name="test"    classname="lib.Phing.tasks.TestTask" />
```