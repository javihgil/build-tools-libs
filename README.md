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

## Tasks

### Call task

Executes a phing target. Extends original [phingcall](http://www.phing.info/docs/guide/stable/apbs25.html) 
task, except that it's execution is optional when not *required*.

**Example**

```xml
    <call target="target-name" required="true"/>
```

**Parameters**

- target: (string) name of target to execute.
- required: (bool, default false) if target definition is required.
- inheritAll: (bool, default: true)
- inheritRefs: (bool, default: false)

### Code Copy task

Copies code from source directory to target directory. Internally uses rsync command, so it's quicker 
than phing's [copy](http://www.phing.info/docs/guide/stable/apbs09.html) task. 

**Example**

```xml
    <ccopy from="src" to="target/build" stats="true" logFile="target/build.copy.log" excludes="build.xml"/>
```

**Parameters**

- from: (string) source directory
- to: (string) target directory
- logFile: (string) log file to store the command result
- excludes: (string) exclude files and directories.
- stats: (bool, default false) show process stats at the end of execution.

### Composer task

#### Actions

**Example**

**Parameters**

### Dev task

#### Actions

**Example**

**Parameters**

### Info task

*TODO*

### Module Iterator task

*TODO*

### Package task

*TODO*

### Repository task

#### Actions

**Example**

**Parameters**

### Rm task

*TODO*

### Symfony task

#### Actions

**Example**

**Parameters**

### Test task

#### Actions

**Example**

**Parameters**
